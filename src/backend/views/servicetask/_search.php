<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ServicetaskcustomstateSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="servicetaskcustomstates-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdServiceTask') ?>

    <?= $form->field($model, 'IdState') ?>

    <?= $form->field($model, 'DateStart') ?>

    <?= $form->field($model, 'DateEnd') ?>

    <?php // echo $form->field($model, 'IdUserCreate') ?>

    <?php // echo $form->field($model, 'IdUserDisabled') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
