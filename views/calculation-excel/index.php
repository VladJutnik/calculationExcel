<?php

use kartik\select2\Select2;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;


?>

<style>
    .flex {
        display: flex;
        flex-wrap: wrap;

        max-width: 2400px; /* макс ширина */
        margin: 0 auto; /* выровняем по центру */
    }

    .item {
        flex: 2 2 calc(50% - 30px); /* отнимем margin и скажем растягиваться */
        margin: 5px;
        box-sizing: border-box; /* чтобы внутренний отступ не влиял когда там будет текст... */
        min-width: 200px; /* мин. ширина блока, чтобы переносились на другой ряд */

        padding: 0px 10px 0px 10px; /*отсутп внутри элемента верх право низ лево */
        font-size: 100%; /*размер шрифта*/
        text-align: center; /*выранвнивение текста по центру*/
        /*background: #b5d8b7; /* цвет фона */
    }
</style>
<br><br>
<b>Добавить когда будет БД, для анализа:</b><br>
- Рост респондента<br>
- Вес респондента<br>
- Код школы респондента<br>
- Возрастная группа респондента<br>
- Порядковый номер анкеты респондента<br>
<br>
<div class="flex">
    <div class="item">
        <?php
        $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

        <?= $form->field($model, 'nameExercise')
            ->widget(
                Select2::classname(),
                [
                    'data' => [
                        '1' => 'Упражнение 1',
                        '2' => 'Упражнение 2',
                        '3' => 'Упражнение 3',
                    ],
                    'options' => ['placeholder' => 'Не указано',
                        'allowClear' => true,
                    ],
                    'pluginOptions' => ['allowClear' => true]
                ])->label('Выберете упражнение') ?>
        <div class="row">
            <div class="col-6 text-center">
                <div class="m-2 text-center font-weight-bold">Данные с первой камеры:</div>
                <?= $form->field($model, 'fileCam1')->fileInput()->label(false) ?>
            </div>
            <div class="col-6 text-center">
                <div class="m-2 text-center font-weight-bold">Данные со второй камеры:</div>
                <?= $form->field($model, 'fileCam2')->fileInput()->label(false) ?>
            </div>
        </div>
        <?= $form->field($model, 'pointSelection', [
            'options' => ['class' => 'row mt-2 mr-1'],
            'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
        ])->dropDownList($arrayPoints, [
            'maxlength' => true,
            'class' => 'form-control col-6'
        ])->label('Выберете точку/и для расчета') ?>
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'frameSelectionStart', [
                    'options' => ['class' => 'row mt-2 mr-1'],
                    'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
                ])->textInput([
                    'type' => 'number',
                    'step' => '1',
                    'min' => '0',
                    'maxlength' => true,
                    'class' => 'form-control col-6',
                ])->label('Начать анализ с кадра:') ?>
            </div>
            <div class="col-6">
                <?= $form->field($model, 'frameSelectionEnd', [
                    'options' => ['class' => 'row mt-2 mr-1'],
                    'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
                ])->textInput([
                    'type' => 'number',
                    'step' => '1',
                    'min' => '0',
                    'maxlength' => true,
                    'class' => 'form-control col-6',
                ])->label('Завершить кадром:') ?>
            </div>
        </div>
        <?= $form->field($model, 'coordinateZ', [
            'options' => ['class' => 'row mt-2 mr-1'],
            'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
        ])->textInput([
            'type' => 'number',
            'step' => '1',
            'min' => '1',
            'maxlength' => true,
            'class' => 'form-control col-6',
        ])->label('Значение для координаты Z') ?>
        <?= $form->field($model, 'pixelWidthFactor', [
            'options' => ['class' => 'row mt-2 mr-1'],
            'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
        ])->textInput([
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'maxlength' => true,
            'class' => 'form-control col-6',
        ])->label('Коэффициент ширины пикселей(мм карт пл)') ?>
        <?= $form->field($model, 'speedFactor', [
            'options' => ['class' => 'row mt-2 mr-1'],
            'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
        ])->textInput([
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'maxlength' => true,
            'class' => 'form-control col-6',
        ])->label('Коэффициент скорости(мм)') ?>
        <?= $form->field($model, 'approximateNoise', [
            'options' => ['class' => 'row mt-2 mr-1'],
            'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
        ])->textInput([
            'type' => 'number',
            'step' => '0.01',
            'min' => '0',
            'maxlength' => true,
            'class' => 'form-control col-6',
        ])->label('Примерный шум') ?>
        <?= $form->field($model, 'rateChangeValues', [
            'options' => ['class' => 'row mt-2 mr-1'],
            'labelOptions' => ['class' => 'col-6 col-form-label font-weight-bold']
        ])->textInput([
            'type' => 'number',
            'step' => '0.0001',
            'min' => '0',
            'maxlength' => true,
            'class' => 'form-control col-6',
        ])->label('Скорость изменения значений') ?>




        <button class="btn btn-secondary btn-block mt-3">Загрузить</button>


        <?php ActiveForm::end() ?>
    </div>
    <div class="item">
        <h5>Последние загруженные файлы</h5>
        <img src="../point.png" alt="" style="max-width: 250px; max-height: 450px">
        <br>
        <br>
       <!-- <h5>Последние загруженные файлы</h5>

        <table class="table table-bordered table-sm">
            <tr>
                <th class="text-center">#</th>
                <th class="text-center">Кто загружал</th>
                <th class="text-center">Организация</th>
                <th class="text-center">Файл</th>
                <th class="text-center">Дата</th>
            </tr>
        </table>-->
    </div>
</div>

<?
if ($resultTable) { ?>
    <div class="container">
        <div class="table-responsive">
            <input type="button" class="btn btn-warning btn-block table2excel mb-1"
                   title="Вы можете скачать в формате Excel" value="Скачать в Excel" id="pechat222">
            <table id="tableId" class="table table-bordered table-sm table2excel_with_colors">
                <?
                foreach ($resultTable as $pointName => $pointCoordinate) {
                    $i = 1;
                    ?>
                    <tr>
                        <th align="center" class="text-center" colspan="12"><?= $pointName ?></th>
                    </tr>
                    <tr>
                        <th class="text-center" align="center">№ координаты</th>
                        <th class="text-center" align="center">X</th>
                        <th class="text-center" align="center">Y</th>
                        <th class="text-center" align="center">Z</th>
                        <th class="text-center" align="center">column1</th>
                        <th class="text-center" align="center">column2</th>
                        <th class="text-center" align="center">пикс/1 60 сек</th>
                        <th class="text-center" align="center">ММ карт пл</th>
                        <th class="text-center" align="center">ММ</th>
                        <th class="text-center" align="center">М</th>
                        <th class="text-center" align="center">М/С</th>
                        <th class="text-center" align="center">М/С2</th>
                    </tr>
                    <?
                    foreach ($pointCoordinate as $coordinateStrKey => $coordinateStr) {
                        ?>
                        <tr>
                            <td class="text-center"><?= $i++ ?></td>
                            <td class="text-center"><?= $coordinateStr['coordinateCam1'] ?></td>
                            <td class="text-center"><?= $coordinateStr['coordinateCam2'] ?></td>
                            <td class="text-center"><?= $coordinateStr['coordinateZ'] ?></td>
                            <td><?= $coordinateStr['column1'] ?></td>
                            <td><?= $coordinateStr['column2'] ?></td>
                            <td><?= $coordinateStr['column3'] ?></td>
                            <td><?= $coordinateStr['column4'] ?></td>
                            <td><?= $coordinateStr['column5'] ?></td>
                            <td><?= $coordinateStr['column6'] ?></td>
                            <td><?= $coordinateStr['column7'] ?></td>
                            <td><?= $coordinateStr['column8'] ?></td>
                        </tr>
                        <?
                    } ?>
                    <?
                } ?>
            </table>
        </div>
    </div>
    <?
} ?>


<?
$script = <<< JS
                               
    $("#pechat222").click(function () {
    var table = $('#tableId');
        if (table && table.length) {
            var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
            $(table).table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "Данные по точкам.xls",
                fileext: ".xls",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: preserveColors
            });
        }
    });                        
    
    $("#pechat333").click(function () {
    var table = $('#tableId2');
        if (table && table.length) {
            var preserveColors = (table.hasClass('table2excel_with_colors') ? true : false);
            $(table).table2excel({
                exclude: ".noExl",
                name: "Excel Document Name",
                filename: "Пункты по которым были выявлены противопоказания.xls",
                fileext: ".xls",
                exclude_img: true,
                exclude_links: true,
                exclude_inputs: true,
                preserveColors: preserveColors
            });
        }
    });

JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
