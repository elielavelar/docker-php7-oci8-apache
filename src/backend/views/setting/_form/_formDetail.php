<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Settingsdetail */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$frmName = 'form-' . $tableName;
?>
<?php $form = ActiveForm::begin([
        'id' => $frmName
    ]);
?>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true, ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Code')->textInput(['maxlength' => true, ]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'Value')->textInput(['maxlength' => true,]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'Sort')->input('number',[]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates()) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes()) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['maxlength' => true, 'rows'=> 4]) ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'IdSetting')->hiddenInput(['id' => $tableName.'-idsetting'])->label(false) ?>
<?php ActiveForm::end(); ?>