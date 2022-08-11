<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\FieldsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="fields-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'Name') ?>

    <?= $form->field($model, 'KeyWord') ?>

    <?= $form->field($model, 'Code') ?>

    <?= $form->field($model, 'IdType') ?>

    <?php // echo $form->field($model, 'IdState') ?>

    <?php // echo $form->field($model, 'HasCatalog') ?>

    <?php // echo $form->field($model, 'Description') ?>

    <?php // echo $form->field($model, 'Value') ?>

    <?php // echo $form->field($model, 'MultipleValue') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
