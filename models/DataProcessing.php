<?php

namespace backend\modules\calculationExcel\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;

class DataProcessing extends Model
{
    public $arrayPointsName;
    public $pointSelection;//выбор точки
    public $frameSelectionStart;//Выбор кадра Start
    public $frameSelectionEnd;//Выбор кадра End
    public $coordinateZ;//координата Z
    public $pixelWidthFactor;//Коэффициент ширины пикселей
    public $speedFactor;//Коэффициент скорости
    public $approximateNoise;//Примерный шум
    public $rateChangeValues;//Скорость изменения значений

    function __construct($arrayPoints, $pointSelection, $frameSelectionStart,$frameSelectionEnd,$coordinateZ,$pixelWidthFactor, $speedFactor, $approximateNoise, $rateChangeValues) {
        $this->arrayPointsName = $arrayPoints;
        $this->pointSelection = $pointSelection;
        $this->frameSelectionStart = $frameSelectionStart;
        $this->frameSelectionEnd = $frameSelectionEnd;
        $this->coordinateZ = $coordinateZ;
        $this->pixelWidthFactor = $pixelWidthFactor;
        $this->speedFactor = $speedFactor;
        $this->approximateNoise = $approximateNoise;
        $this->rateChangeValues = $rateChangeValues;
    }

    public function resultProcessingData($arrayCam1, $arrayCam2)
    {
        //объединяем кординаты из двух файлов
        $resultBuildTwoFile = $this->buildingTwoFiles($arrayCam1, $arrayCam2); // результат объеденения данных

        $resultZeroRemoveLinear = $this->zeroRemoveLinear($resultBuildTwoFile); // результат объеденения данных

        //Применяем Калман дла массива
        $resultСalculationKalman = $this->calculationKalman($resultZeroRemoveLinear);



        //Расчет координаты Z
        //$resultСalculationKalman = $resultBuildTwoFile; // убрать после просчета калмана
        $resultcalculationCoordinateZ = $this->calculationCoordinateZ($resultСalculationKalman); //массив с координатой Z
        //print_r('<pre>');
        //print_r($resultcalculationCoordinateZ);
        //print_r('<br><br><br>');
        //exit();
        //возращаем просчтитанные данные с координатами
        return $resultcalculationCoordinateZ;
    }

    private function buildingTwoFiles($arrayCam1, $arrayCam2){

        //print_r('<pre>');
        //print_r($this->pointSelection);
        //print_r('<br><br><br>');
        //print_r($this->frameSelectionStart);
        //print_r('<br><br><br>');
        //print_r($this->frameSelectionEnd);
        //print_r('<br><br><br>');
        //print_r($this->arrayPointsName[$this->pointSelection]);
        //print_r('<br><br>');
        //exit();

        $result = [];

        if($this->pointSelection == '0w'){
            foreach ($this->arrayPointsName as $keypoint => $pointNameB){
                $keyCam1Points = array_search($pointNameB, $arrayCam1[0]); //нашил в файлах номер столбца по точкам
                $keyCam2Points = array_search($pointNameB, $arrayCam2[0]); //нашил в файлах номер столбца по точкам
                $iStr = ($this->frameSelectionStart != 0) ? $this->frameSelectionStart : 1;
                $iStrEnd = ($this->frameSelectionEnd != 0) ? $this->frameSelectionEnd : count($arrayCam1);
                for($iStr; $iStr < $iStrEnd; $iStr++){
                    //$result[$pointNameB][] = [
                    //    'coordinateCam1' => round($arrayCam1[$iStr][$keyCam1Points], 2),
                    //    'coordinateCam2' => round($arrayCam2[$iStr][$keyCam2Points], 2),
                    //    //это расчет координаты Z пока нет Калмана, когда будет калман нужно будет пересчитывать после Калмана
                    //    //'coordinateZ' => round($coordinateZindex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2), 2), //$coordinateZindex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2)
                    //];
                    //print_r('<br><br>');
                    //print_r($pointNameB);
                    //exit();
                    $result[$pointNameB]['coordinateCam1'][] =  round($arrayCam1[$iStr][$keyCam1Points], 2);
                    $result[$pointNameB]['coordinateCam2'][] =  round($arrayCam2[$iStr][$keyCam2Points], 2);
                }
            }
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r(count($result));
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r($pointNameB.' - ' . $keyCam1Points . '; '.$keyCam2Points);
            //print_r('<br><br>');
            //exit();
        } else {
            $iStr = ($this->frameSelectionStart != 0) ? $this->frameSelectionStart : 1;
            $iStrEnd = ($this->frameSelectionEnd != 0) ? $this->frameSelectionEnd : count($arrayCam1);
            $keyCam1Points = array_search($this->arrayPointsName[$this->pointSelection], $arrayCam1[0]); //нашил в файлах номер столбца по точкам
            $keyCam2Points = array_search($this->arrayPointsName[$this->pointSelection], $arrayCam2[0]); //нашил в файлах номер столбца по точкам
            for($iStr; $iStr < $iStrEnd; $iStr++){
                //$result[$this->arrayPointsName[$this->pointSelection]][] = [
                //    'coordinateCam1' => round($arrayCam1[$iStr][$keyCam1Points], 2),
                //    'coordinateCam2' => round($arrayCam2[$iStr][$keyCam2Points], 2),
                //    //это расчет координаты Z пока нет Калмана, когда будет калман нужно будет пересчитывать после Калмана
                //    //'coordinateZ' => round($coordinateZindex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2), 2), //$coordinateZindex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2)
                //];
                $result[$this->arrayPointsName[$this->pointSelection]]['coordinateCam1'][] =  round($arrayCam1[$iStr][$keyCam1Points], 2);
                $result[$this->arrayPointsName[$this->pointSelection]]['coordinateCam2'][] =  round($arrayCam2[$iStr][$keyCam2Points], 2);
            }
            //print_r(count($result));
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r($pointName.' - ' . $keyCam1Points . '; '.$keyCam2Points);
            //print_r('<br><br>');

        }
        //print_r('<pre>');
        //print_r($this->arrayPointsName);
        //print_r('<br><br><br>');
        //print_r($result);
        //print_r('<br><br><br>');
        //exit();
        return $result;
        //print_r('<pre>');
        //print_r($result);
        //print_r('<br><br><br><br><br><br>');
        ////print_r($this->arrayPointsName);
        ////print_r($arrayCam1[0]);
        //print_r('<br><br>');
        ////for($i = 1; $i < count($arrayCam1[0]); $i++){
        ////    for($j = 1; $j < count($arrayCam1[$i]); $j++){
        ////          print_r($arrayCam1[$i][$j]);
        ////          print_r('<br><br>');
        ////    }
        ////}
        //print_r('</pre>');
        //exit();
    }

    private function zeroRemoveLinear($result){
        //print_r('<pre>');
        //print_r($result);
        //print_r('<br><br><br>');
        //exit();
        $resultNew = $result;
        foreach ($result as $keyPointCoor => $pointCoordinate){
            foreach ($pointCoordinate as $keylinear_filter => $linear_filter){

                foreach ($linear_filter as $key => $value){
                    //print_r('<pre>');
                    //print_r($resultNew[$keyPointCoor][$keylinear_filter][$key]);
                    //print_r('<br><br><br>----------------');;
                    //print_r($value);
                    //print_r('<br><br><br>');
                    //print_r('$keyPointCoor '.$keyPointCoor);
                    //print_r('<br><br><br>');
                    //print_r('$keylinear_filter '.$keylinear_filter);
                    //print_r('<br><br><br>');
                    //print_r('$key '.$key);
                    //print_r('<br><br><br>');
                    //exit();

                    if ($value == 0 and $resultNew[$keyPointCoor][$keylinear_filter][$key] == 0){

                        $y1 = $linear_filter[$key-1];
                        $x1 = $key-1;

                        $keywhile = $key+1;
                        while ($linear_filter[$keywhile] == 0){
                            $keywhile++;
                        }
                        $y2 = $linear_filter[$keywhile];
                        $x2 = $keywhile;

                        $m = ($y2-$y1)/($x2-$x1);
                        $b = $y1 - ($m*$x1);

                        $iwhile = $x1+1;
                        while ($iwhile < $x2){
                            //print_r('<br>');
                            //print_r($iwhile);
                            //  $linear_filter[$iwhile] = round($m*$iwhile+$b,2);
                            $resultNew[$keyPointCoor][$keylinear_filter][$iwhile] = round($m*$iwhile+$b,2);


                            $iwhile++;

                            // print_r('<pre>');
                            // print_r($x1);
                            // print_r('<br>');
                            // print_r($x2);
                            // print_r('<br>');
                            // print_r($y1);
                            // print_r('<br>');
                            // print_r($y2);
                            // print_r('<br>');
                            // print_r($resultNew[$keyPointCoor][$keylinear_filter]);
                        }
                        $x1 = 0;
                        $x2 = 0;
                        $y1 = 0;
                        $y2 = 0;
                    }

                }

                //print_r('-------------21121213---------------------');
                //print_r('<br>');
            }

            //$linear_filter = $pointCoordinate;
            //print_r('<pre>');
            //print_r($keyPointCoor);
            //print_r('<br><br><br>');
            //print_r($linear_filter);
            //print_r('<br><br><br> 2232323232');
            //print_r($resultNew);
            //print_r('<br><br><br>');
            //exit();


        }
        //print_r('<pre>');
        //print_r($result);
        //print_r('<br>');
        //print_r('<br>');
        //print_r('<br>');
        //print_r('<br>');
        //print_r('----------------11111111------------');
        //print_r($resultNew);
        //print_r('<br>');
        //exit();


        return $resultNew;
    }

    //ТУТ ФУНКЦИЯ КАЛЬКУЛИРУЕт МЕТОД КАЛМАНА ЕСЛИ ЧТО ДОПИСЫВАЕМ ФУНКЦИИ И и все возращаем ее она уже передаст в наш главный метод!!!!
    private function calculationKalman($result){

        $newResultKalman = [];
        foreach ($result as $keyPointCoor => $pointCoordinate){
            foreach ($pointCoordinate as $keylinear_filter => $linear_filter){
                //print_r('<pre>');
                //print_r($linear_filter);
                //print_r('<br><br><br>');
                //print_r($pointCoordinate);
                //print_r('<br><br><br>');
                //exit();
                $_err_measure = $this->approximateNoise; // примерный шум измерений
                $_q = $this->rateChangeValues; // скорость изменения значений 0.001-1, варьировать самому
                $_kalman_gain = 0;
                $_current_estimate = 0;
                $_err_estimate = $_err_measure;
                foreach ($linear_filter as $key => $value){
                    $_kalman_gain = $_err_estimate/($_err_estimate + $_err_measure);
                    $_current_estimate = $_last_estimate + $_kalman_gain * ($value - $_last_estimate);
                    $_err_estimate =  (1.0 - $_kalman_gain) * $_err_estimate + abs($_last_estimate - $_current_estimate) * $_q;
                    $_last_estimate = $_current_estimate;
                    $newResultKalman[$keyPointCoor][$keylinear_filter][$key] = round($_current_estimate,2);
                }

            }
        }
        //print_r('<pre>');
        //print_r($newResultKalman);
        //print_r('<br><br><br>');
        //exit();
        //$_err_measure = 1; // примерный шум измерений
        //$_q = 0.001; // скорость изменения значений 0.001-1, варьировать самому
        //$_kalman_gain = 0;
        //$_current_estimate = 0;
        //$_err_estimate = $_err_measure;
        //$newResultKalman = [];
        //foreach ($newResult['X6']['coordinateCam1'] as $one){
        //    $_kalman_gain = $_err_estimate/($_err_estimate + $_err_measure);
        //    $_current_estimate = $_last_estimate + $_kalman_gain * ($one - $_last_estimate);
        //    $_err_estimate =  (1.0 - $_kalman_gain) * $_err_estimate + abs($_last_estimate - $_current_estimate) * $_q;
        //    $_last_estimate = $_current_estimate;
        //    $newResultKalman['X6']['coordinateCam1'][] = round($_current_estimate,2);
        //}
        //print_r('<pre>');
        //print_r($newResultKalman);
        //print_r('<br><br><br>');
        //print_r($newResult);
        //print_r('<br><br><br>');
        //print_r($result);
        //print_r('<br><br>');
        //exit();

        return $newResultKalman;
    }

    //Расчет координаты Z
    private function calculationCoordinateZ($resultStart){
        $result = [];
        foreach ($resultStart as $pointName => $pointCoordinate){
            //print_r('<pre>');
            //print_r($pointCoordinate);
            //print_r('<br><br>');
            //exit();
            for ($i=0;$i<count($pointCoordinate['coordinateCam1']); $i++){
                $result[$pointName][] = [
                    'coordinateCam1' => $pointCoordinate['coordinateCam1'][$i],
                    'coordinateCam2' => $pointCoordinate['coordinateCam2'][$i],
                    'coordinateZ' => round($this->coordinateZ-(($pointCoordinate['coordinateCam1'][$i] + $pointCoordinate['coordinateCam2'][$i])/2), 2), //$coordinateZIndex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2)
                ];
            }

        }
        //print_r('<pre>');
        //print_r($result);
        //print_r('<br><br>');
        //exit();
        return $result;
    }
}
?>