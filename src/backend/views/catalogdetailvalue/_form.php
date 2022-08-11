<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetailvalues */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="catalogdetailvalues-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'IdCatalogDetail')->textInput() ?>

    <?= $form->field($model, 'IdDataType')->textInput() ?>

    <?= $form->field($model, 'IdValueType')->textInput() ?>

    <?= $form->field($model, 'Value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
