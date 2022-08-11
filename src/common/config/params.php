<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
    'bsVersion' => '4.x',
    'bsDependencyEnabled' => true,
    'company'=>[
        'name'=>'Muehlbauer ID Services',
        'defaultCountry' => 'SV',
        'matchCountryInstance' => false,
        'logo' => 'img/logo.png',
        'logo-sm' => 'img/smalllogo.png',
        'logo-min' => 'img/logo-alt.png',
    ],
    'hail812/yii2-adminlte3' => [
        'pluginMap' => [
            'sweetalert2' => [
                'css' => 'sweetalert2-theme-bootstrap-4/bootstrap-4.min.css',
                'js' => 'sweetalert2/sweetalert2.min.js'
            ]
        ]
    ],
    'api' => [
        'resource' => [
            'url' => 'http://localhost:3000',
            'class' => 'common\components\HttpClient\CurlClient',
        ]
    ],
];
