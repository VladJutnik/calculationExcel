<?php

namespace backend\modules\calculationExcel\models;

use Yii;
use yii\base\Model;
use yii\rbac\DbManager;

class CalculateLoad extends Model
{
    public $fileCam1;
    public $fileCam2;
    public $nameExercise; //название упражнения
    public $pointSelection; //выбор точки
    public $frameSelectionStart; //Выбор кадра Start
    public $frameSelectionEnd; //Выбор кадра End
    public $coordinateZ = 1080; //координата Z
    public $pixelWidthFactor = 0.35; //Коэффициент ширины пикселей
    public $speedFactor = 9.95; //Коэффициент скорости
    public $approximateNoise = 0.5; //Примерный шум
    public $rateChangeValues = 0.01; //Скорость изменения значений

    public $anglePoint;
    public $frameSelection;

    public function randomFileName($path, $extension)
    {
        do {
            $name = mt_rand(0, 999999999);
            $file = $path . $name . '.'. $extension;
        } while (file_exists($file));
        $name2 = $name . '.'. $extension;
        return $name2;
    }

}?>