<?php

namespace backend\modules\calculationExcel\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;

class TableCalculations extends Model
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

    public function resultTableCalc($array)
    {
        $result = [];

        foreach ($array as $pointName => $pointCoordinate){
            $i = 0;
            foreach ($pointCoordinate as $coordinateStrKey => $coordinateStr){
                if($coordinateStrKey != 0){
                    if($coordinateStrKey != 1){
                        $column1 = round(pow(($array[$pointName][$coordinateStrKey-1]['coordinateCam1'] - $array[$pointName][$coordinateStrKey]['coordinateCam1']), 2),2);
                        $column2 = round(pow(($array[$pointName][$coordinateStrKey-1]['coordinateCam2'] - $array[$pointName][$coordinateStrKey]['coordinateCam2']), 2),2);
                        $column3 = round(pow(($column1 + $column2), 0.5),2);
                        $column4 = round($column3 * $this->pixelWidthFactor,2);
                        $column5 = round($column4 * $this->speedFactor,2);
                        $column6 = round($column5 / 1000,2);
                        $column7 = round($column6 * 60,2);
                        $column8 = round($column7 - $result[$pointName][$i-1]['column7'],2);
                        $result[$pointName][$i] = [
                            'coordinateCam1' => $coordinateStr['coordinateCam1'],
                            'coordinateCam2' => $coordinateStr['coordinateCam2'],
                            'coordinateZ' => $coordinateStr['coordinateZ'],
                            'column1' => $column1,
                            'column2' => $column2,
                            'column3' => $column3,
                            'column4' => $column4,
                            'column5' => $column5,
                            'column6' => $column6,
                            'column7' => $column7,
                            'column8' => $column8,
                        ];
                    } else {
                        $column1 = round(pow(($array[$pointName][$coordinateStrKey-1]['coordinateCam1'] - $array[$pointName][$coordinateStrKey]['coordinateCam1']), 2),2);
                        $column2 = round(pow(($array[$pointName][$coordinateStrKey-1]['coordinateCam2'] - $array[$pointName][$coordinateStrKey]['coordinateCam2']), 2),2);
                        $column3 = round(pow(($column1 + $column2), 0.5),2);
                        $column4 = round($column3 * 0.35,2);
                        $column5 = round($column4 * 9.95,2);
                        $column6 = round($column5 / 1000,2);
                        $column7 = round($column6 * 60,2);
                        $result[$pointName][$i] = [
                            'coordinateCam1' => $coordinateStr['coordinateCam1'],
                            'coordinateCam2' => $coordinateStr['coordinateCam2'],
                            'coordinateZ' => $coordinateStr['coordinateZ'],
                            'column1' => $column1,
                            'column2' => $column2,
                            'column3' => $column3,
                            'column4' => $column4,
                            'column5' => $column5,
                            'column6' => $column6,
                            'column7' => $column7,
                            'column8' => '',
                        ];
                    }

                } else {
                    $result[$pointName][$i] = [
                        'coordinateCam1' => $coordinateStr['coordinateCam1'],
                        'coordinateCam2' => $coordinateStr['coordinateCam2'],
                        'coordinateZ' => $coordinateStr['coordinateZ'],
                        'column1' => '',
                        'column2' => '',
                        'column3' => '',
                        'column4' => '',
                        'column5' => '',
                        'column6' => '',
                        'column7' => '',
                        'column8' => '',
                    ];
                }
                $i++;
            }
        }
        return $result;
    }
/*
    private function buildingTwoFiles($arrayCam1, $arrayCam2, $nuStrStart = 1, $nuStrEnd = 100, $coordinateZindex = 1080){
        $result = [];
        foreach ($this->arrayPointsName as $keypoint => $pointName){
            $keyCam1Points = array_search($pointName, $arrayCam1[0]); //нашил в файлах номер столбца по точкам
            $keyCam2Points = array_search($pointName, $arrayCam2[0]); //нашил в файлах номер столбца по точкам
            for($iStr = 1; $iStr< count($arrayCam1); $iStr++){
                $result[$pointName][] = [
                    'coordinateCam1' => round($arrayCam1[$iStr][$keyCam1Points], 2),
                    'coordinateCam2' => round($arrayCam2[$iStr][$keyCam2Points], 2),
                    //это расчет координаты Z пока нет Калмана, когда будет калман нужно будет пересчитывать после Калмана
                    //'coordinateZ' => round($coordinateZindex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2), 2), //$coordinateZindex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2)
                ];
            }
            //print_r(count($result));
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r('<br><br>');
            //print_r($pointName.' - ' . $keyCam1Points . '; '.$keyCam2Points);
            //print_r('<br><br>');
        }
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

    //ТУТ ФУНКЦИЯ КАЛЬКУЛИРУЕт МЕТОД КАЛМАНА ЕСЛИ ЧТО ДОПИСЫВАЕМ ФУНКЦИИ И и все возращаем ее она уже передаст в наш главный метод!!!!
    private function calculationKalman($result){
        return $result;
    }
    //Расчет координаты Z
    private function calculationCoordinateZ($resultStart, $coordinateZIndex){
        $result = [];
        foreach ($resultStart as $pointName => $pointCoordinate){
            foreach ($pointCoordinate as $coordinateStrKey => $coordinateStr){
                $result[$pointName][] = [
                    'coordinateCam1' => $coordinateStr['coordinateCam1'],
                    'coordinateCam2' => $coordinateStr['coordinateCam2'],
                    'coordinateZ' => round($coordinateZIndex-(($coordinateStr['coordinateCam1'] + $coordinateStr['coordinateCam2'])/2), 2), //$coordinateZIndex-(($arrayCam1[$iStr][$keyCam1Points] + $arrayCam2[$iStr][$keyCam2Points])/2)
                ];
            }
        }
        //print_r('<pre>');
        //print_r($result);
        //print_r('<br><br>');
        //exit();
        return $result;
    }*/
}?>