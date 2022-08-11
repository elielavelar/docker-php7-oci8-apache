<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\HolidaySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="holidays-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'Description') ?>

    <?= $form->field($model, 'IdType') ?>

    <?= $form->field($model, 'IdState') ?>

    <?php // echo $form->field($model, 'DateStart') ?>

    <?php // echo $form->field($model, 'DateEnd') ?>

    <?php // echo $form->field($model, 'IdFrequencyType') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
