<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CatalogdetailvaluesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalogdetailvalues-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'Id') ?>

    <?= $form->field($model, 'IdCatalogDetail') ?>

    <?= $form->field($model, 'IdDataType') ?>

    <?= $form->field($model, 'IdValueType') ?>

    <?= $form->field($model, 'Value') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
