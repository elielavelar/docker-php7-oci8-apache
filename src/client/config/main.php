<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-client',
    'name'=>'Intranet Muhlbauer',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm',
    ],
    'controllerNamespace' => 'client\controllers',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-client',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
            'identityCookie' => ['name' => '_identity-client', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-client',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<controller:\w+>/<id:\d+>' => '<controller>/view', 
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>', 
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                '<controller:\w+\-\w+>/<id:\d+>' => '<controller>/view',  
                ['class' => 'yii\rest\UrlRule', 'controller' => 'site'],
            ],
        ],
        'customFunctions'=>[
            'class'=>'client\components\CustomFunctions',
        ],
        'view' => [
            'theme' => [
                'class' => \webtoolsnz\AdminLte\Theme::className(),
                'skin' => \webtoolsnz\AdminLte\Theme::SKIN_BLUE,
            ]
        ],
        'pdf' => [
            'class' => \kartik\mpdf\Pdf::class,
            'format' => \kartik\mpdf\Pdf::FORMAT_A4,
            'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
            'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
            // refer settings section for all configuration options
        ],
    ],
    'params' => $params,
];
