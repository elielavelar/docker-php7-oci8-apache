<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PolicyversionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="policyversions-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'Version') ?>

    <?= $form->field($model, 'IdPolicy') ?>

    <?= $form->field($model, 'IdState') ?>

    <?= $form->field($model, 'Description') ?>

    <?php // echo $form->field($model, 'Approved') ?>

    <?php // echo $form->field($model, 'Sent') ?>

    <?php // echo $form->field($model, 'ApprovedDate') ?>

    <?php // echo $form->field($model, 'SentDate') ?>

    <?php // echo $form->field($model, 'ActualVersion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
