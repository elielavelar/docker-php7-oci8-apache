<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\prddui\Anexoacta */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="anexoacta-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'COD_CTRO_SERV')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'COD_JEFE')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'COD_DELEGADO')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FEC_FACTURACION')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'FEC_ACTA')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'NUM_CORR_ACTA')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'PRIMERAVEZ')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'MODIFICACIONES')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'REPOSICIONES')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'RENOVACIONES')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'REIMPRESIONES')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TAR_BASE_ANULADAS')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'TAR_DECAD_ANULADAS')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
