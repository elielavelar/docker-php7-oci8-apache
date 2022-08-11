<?php

use common\customassets\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\customassets\helpers\Nav;
/* @var $assetDir string; */

$items = [];
if( !Yii::$app->getUser()->isGuest){
    $notificationCount = 0;
    $badgeClass = '';
    $items[] = [
        'label' => Html::icon('far fa-bell').Html::tag('span', $notificationCount, ['class' => 'badge navbar-badge '.$badgeClass]),
        'url' => '#',
        'items' => [
            [
                'label' => '0 Notifications',
            ],
            '-',
        ],
        'dropdownOptions' => [
            'tag' => 'div',
            'tagChildren' => 'div',
            'class' => 'dropdown-menu-lg'
        ]
    ];

    $items[] = [
        'label' => Html::icon('fas fa-sign-out-alt'),
        'url' => ['site/logout'],
        'options' => [
            'data' => [
                'method' => 'post',
            ]
        ]
    ];
    $items[] = [
        'label' => Html::img(
            Yii::getAlias('@web/img/avatar.png'), ['alt' => 'User', 'class' => 'user-image img-circle']
        ). Html::tag('span', Yii::$app->getUser()->getIdentity()->DisplayName, [
            'class' => 'd-none d-md-inline'
            ]),
        'url' => '#',
        'items' => [
            [
                'label' => Html::img(
                    Yii::getAlias('@web/img/avatar.png'), ['alt' => 'User', 'class' => 'elevation-2 img-circle']
                ). Html::tag('p', Yii::$app->getUser()->getIdentity()->DisplayName, []),
                'options' => [
                    'class' => 'user-header bg-primary'
                ],
            ], [
                'label' => '',
                'options' => [
                    'class' => 'user-body'
                ],
            ], [
                'label' => Html::tag('span',
                    Html::a('Perfil', ['user/profile'], ['class' => 'btn btn-default btn-flat']),
                        [ 'class' => 'float-left'])
                    . Html::tag('span',Html::a('Cerrar Sesión', ['site/logout'], ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']),
                        [ 'class' => 'float-right']),
                'options' => [
                    'class' => 'user-footer'
                ],
            ],
        ],
        'linkOptions' => [
            'class'=> ''
        ],
        'options' => [
            'class' => 'user-menu'
        ]
    ];
}
?>
<?= Nav::widget([
        'items' => $items,
])?>