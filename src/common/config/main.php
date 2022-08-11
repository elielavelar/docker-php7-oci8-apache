<?php

return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'language'=>'es-SV',
    'version' => '3.0.0',
    'modules' => [
        'gridview'=>[
            'class'=>'\kartik\grid\Module',
        ],
    ],
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'linkAssets' => true,
        ],
        'formatter' => [
            'defaultTimeZone' => 'America/El_Salvador',
            'dateFormat' => 'php:d-m-Y',
            'datetimeFormat' => 'php:d-m-Y H:i',
            'timeFormat' => 'php:h:i:s',
            'decimalSeparator' => '.',
            'thousandSeparator' => ',',
            'currencyCode' => 'USD',
        ],
        'authManager'=>[
            'class'=>'yii\rbac\DbManager',
            'itemTable'=>'authitem', // Tabla que contiene los elementos de autorizacion
            'itemChildTable'=>'authitemchild', // Tabla que contiene los elementos padre-hijo
            'assignmentTable'=>'authassignment', // Tabla que contiene la signacion usuario-autorizacion
        ],
        'customFunctions'=>[
            'class'=>'common\components\CustomFunctions',
        ],
        'appLog'=>[
            'class'=>'common\components\AppLog',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'forceTranslation' => true,
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        //'system' => 'system.php',
                        'app/error' => 'error.php',
                    ],
                ],
                'system*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'forceTranslation' => true,
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'system' => 'system.php',
                    ],
                ],
                'datatable*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
                    'forceTranslation' => true,
                    //'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'datatables' => 'datatables.php',
                    ],
                ],
            ],
        ],
        'pdf' => [
            'class' => \kartik\mpdf\Pdf::class,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            // refer settings section for all configuration options
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'keycloak' => [
                    'class' => \common\components\authclient\KeyCloakClient::class,
                    'clientId' => 'mbclient',
                    'clientSecret' => 'ece63bb9-11e3-4796-be91-88c5069432fb',
                    'issuerUrl' => 'http://localhost:8080/auth',
                    'db' => 'db',
                    'logoutUrl' => 'http://localhost',
                ]
            ]
        ],
    ],
];
