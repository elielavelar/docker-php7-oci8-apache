<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\DatosoperSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="datos-oper-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'COD_OPER') ?>

    <?= $form->field($model, 'PASWD_SISTEMA') ?>

    <?= $form->field($model, 'PASWD_RED') ?>

    <?= $form->field($model, 'NOM1_OPER') ?>

    <?= $form->field($model, 'NOM2_OPER') ?>

    <?php // echo $form->field($model, 'NOM3_OPER') ?>

    <?php // echo $form->field($model, 'APDO1_OPER') ?>

    <?php // echo $form->field($model, 'APDO2_OPER') ?>

    <?php // echo $form->field($model, 'COD_ROL') ?>

    <?php // echo $form->field($model, 'COD_CARGO_OPER') ?>

    <?php // echo $form->field($model, 'STAT_OPER') ?>

    <?php // echo $form->field($model, 'COD_CTRO_SERV') ?>

    <?php // echo $form->field($model, 'COD_EMPLEADO') ?>

    <?php // echo $form->field($model, 'FECHA_CAMBIO') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
