<?php
return [
    'modules' => [
        'calculation-excel' => [
            'class' => 'backend\modules\calculationExcel\CalculationExcelModule',
        ],
    ],
    'components' => [
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/calculation-excel' => 'calculation-excel/calculation-excel/index', //модуль расчета кселя
            ],
        ],

    ],
];