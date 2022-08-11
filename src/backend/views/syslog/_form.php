<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Syslog */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Title')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'LogKey')->textInput(['maxlength' => true, 'readonly'=> TRUE]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'transactionModelName')->textInput(['readonly' => TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-2">
            <?= $form->field($model, 'ActionType')->textInput(['readonly' => TRUE]) ?>
        </div>
        <div class="col-4">
            <?= Html::label('&nbsp;', $tableName.'-actiontypename')?>
            <?= Html::label($model->actionTypeName, NULL, ['class' => 'form-control readonly','id' => $tableName.'-actiontypename'])?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'IdRecord')->textInput(['readonly' => TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'ControllerName')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'ActionName')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'EnvironmentName')->textInput(['readonly' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'userName')->textInput(['readonly' => TRUE]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'CreationDate')->textInput(['readonly' => TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6, 'readonly'=> TRUE]) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
