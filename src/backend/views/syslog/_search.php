<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SyslogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="syslog-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'LogKey') ?>

    <?= $form->field($model, 'IdTransactionModel') ?>

    <?= $form->field($model, 'Title') ?>

    <?= $form->field($model, 'IdRecord') ?>

    <?php // echo $form->field($model, 'ActionType') ?>

    <?php // echo $form->field($model, 'CreationDate') ?>

    <?php // echo $form->field($model, 'IdUser') ?>

    <?php // echo $form->field($model, 'ControllerName') ?>

    <?php // echo $form->field($model, 'ActionName') ?>

    <?php // echo $form->field($model, 'EnvironmentName') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
