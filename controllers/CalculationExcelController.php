<?php

namespace backend\modules\calculationExcel\controllers;

use backend\modules\calculationExcel\models\DataProcessing;
use backend\modules\calculationExcel\models\CalculateLoad;
use backend\modules\calculationExcel\models\TableCalculations;
use PHPExcel_IOFactory;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Default controller for the `calculationExcel` module
 */
class CalculationExcelController extends Controller
{
    protected $arrayPoints = [
        'X0',
        'X1',
        'X2',
        'X3',
        'X4',
        'X5',
        'X6',
        'X7',
        'X8',
        'X9',
        'X10',
        'X11',
        'X12',
        'X13',
        'X14',
        'X15',
        'X16',
        'X17',
        'X18',
        'X19',
        'X20',
        'X21',
        'X22',
        'X23',
        'X24',
    ];
    protected $pointSelection;//выбор точки
    protected $frameSelectionStart;//Выбор кадра Start
    protected $frameSelectionEnd;//Выбор кадра End
    protected $coordinateZ;//координата Z
    protected $pixelWidthFactor;//Коэффициент ширины пикселей
    protected $speedFactor;//Коэффициент скорости
    protected $approximateNoise;//Примерный шум
    protected $rateChangeValues;//Скорость изменения значений

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }
        require_once Yii::$app->basePath . '\Excel\PHPExcel.php';
        require_once Yii::$app->basePath . '\Excel\PHPExcel\IOFactory.php';


        ini_set('max_execution_time', 3600);
        ini_set('memory_limit', '5092M');
        ini_set("pcre.backtrack_limit", "5000000");
        $model = new CalculateLoad();
        $model->frameSelectionStart = 0;
        $model->frameSelectionEnd = 0;
        if (Yii::$app->request->post())
        {
            $post = Yii::$app->request->post()['CalculateLoad'];
            $this->pointSelection = $post['pointSelection'];
            $this->frameSelectionStart = $post['frameSelectionStart'];
            $this->frameSelectionEnd = $post['frameSelectionEnd'];
            $this->coordinateZ = $post['coordinateZ'];
            $this->pixelWidthFactor = $post['pixelWidthFactor'];
            $this->speedFactor = $post['speedFactor'];
            $this->approximateNoise = $post['approximateNoise'];
            $this->rateChangeValues = $post['rateChangeValues'];
            //print_r('<pre>');
            //print_r($post['pointSelection']);
            //print_r('<br><br>');
            //print_r($this->pointSelection);
            //print_r('<br><br>');
            //exit();
            if ($_FILES)
            {
                $path = "CalculateLoad/"; //папака в которой лежит файл
                $extensionCam1 = strtolower(substr(strrchr($_FILES['CalculateLoad']['name']['fileCam1'], '.'), 1));//узнали в каком формате файл пришел
                $extensionCam2 = strtolower(substr(strrchr($_FILES['CalculateLoad']['name']['fileCam2'], '.'), 1));//узнали в каком формате файл пришел
                $file_nameCam1 = $model->randomFileName($path, $extensionCam1); // сделали новое имя с проверкой есть ли такое имя в папке для первой камеры
                $file_nameCam2 = $model->randomFileName($path, $extensionCam2); // сделали новое имя с проверкой есть ли такое имя в папке для второй камеры
                if (move_uploaded_file($_FILES['CalculateLoad']['tmp_name']['fileCam1'], $path . $file_nameCam1) && move_uploaded_file($_FILES['CalculateLoad']['tmp_name']['fileCam2'], $path . $file_nameCam2))
                {
                    $excelCam1 = PHPExcel_IOFactory::load($path.$file_nameCam1);
                    $excelCam2 = PHPExcel_IOFactory::load($path.$file_nameCam2);
                    foreach($excelCam1->getWorksheetIterator() as $worksheet) {
                        $listsCam1[] = $worksheet->toArray();
                    }
                    foreach($excelCam2->getWorksheetIterator() as $worksheet) {
                        $listsCam2[] = $worksheet->toArray();
                    }
                    //$listsCam1[0] первый лист файла
                    //$listsCam2[0] второй лист файла
                    if (count($listsCam1[0][0]) == count($listsCam2[0][0])){
                        $dataProcessing = new DataProcessing($this->arrayPoints, $this->pointSelection, $this->frameSelectionStart, $this->frameSelectionEnd, $this->coordinateZ, $this->pixelWidthFactor, $this->speedFactor, $this->approximateNoise, $this->rateChangeValues);
                        $tableCalculations = new TableCalculations($this->arrayPoints, $this->pointSelection, $this->frameSelectionStart, $this->frameSelectionEnd, $this->coordinateZ, $this->pixelWidthFactor, $this->speedFactor, $this->approximateNoise, $this->rateChangeValues);

                        $result = $dataProcessing->resultProcessingData($listsCam1[0], $listsCam2[0]); //обработанный массив
                        $resultTable = $tableCalculations->resultTableCalc($result);
                        //////print_r($DataProcessing);
                        //print_r('<pre>');
                        //print_r('<br><br>');
                        //print_r($resultTable);
                        //print_r('<br><br>');
                        //exit();

                        //$DataProcessing->randomFileName();
                    } else {
                        Yii::$app->session->setFlash('error', "Количество строк в двух файлов не совпадают!");
                    }

                    //print_r($DataProcessing->randomFileName());
                    //foreach($listsCam1[0] as $list){
                    //    echo '<table border="1">';
                    //    // Перебор строк
                    //    foreach($listsCam1[0] as $row){
                    //        echo '<tr>';
                    //        // Перебор столбцов
                    //        foreach($row as $col){
                    //            echo '<td>'.$col.'</td>';
                    //        }
                    //        echo '</tr>';
                    //    }
                    //    echo '</table>';
                    //}

                    //print_r('<pre>');
                    //print_r($listsCam1[0]);
                    //print_r('<br><br>');
                    //print_r($listsCam2[0]);
                    //print_r('<br><br>');
                    //print_r(Yii::$app->basePath);
                    //print_r('<br><br>');
                    //print_r($_FILES);
                    //print_r('</pre>');
                    //exit();
                }
                else {
                    Yii::$app->session->setFlash('error', "Не удалось загрузить файл!");
                }
            }
            else {
                Yii::$app->session->setFlash('error', "Что то пошло не так!");
            }
        }
        return $this->render('index', [
            'model' => $model,
            'resultTable' => $resultTable,
            //'arrayPoints' => ArrayHelper::merge(['0w' => 'Расчет по всем точкам'], $this->arrayPoints),
            'arrayPoints' => $this->arrayPoints,
        ]);
    }


}
