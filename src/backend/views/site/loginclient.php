<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;
/* @var $model \common\models\LoginForm */
/* @var $this \yii\web\View */
$this->title = '';
?>
<?= yii\authclient\widgets\AuthChoice::widget([
    'baseAuthUrl' => ['site/auth'],
    'popupMode' => false,
]) ?>