<?php

use yii\helpers\Html;
use kartik\password\PasswordInput;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */

$options = [
    'maxlength'=>TRUE,
    'disabled'=>TRUE,
];
$addoptions = $options;
if(!$model->isNewRecord){
    $addoptions['disabled']= $model->disabled;
}

$verdictTitles = [
    0 => 'No Ingresada',
    1 => 'Muy Débil',
    2 => 'Débil',
    3 => 'Aceptable', 
    4 => 'Buena',
    5 => 'Excelente'
];

?>
<div class="card-body">
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Username')->textInput($options) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'FirstName')->textInput($addoptions) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'SecondName')->textInput($addoptions) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'LastName')->textInput($addoptions) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'SecondLastName')->textInput($addoptions) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'DisplayName')->textInput($addoptions) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Email')->textInput($addoptions) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'CodEmployee')->textInput($options) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'serviceCentreName')->textInput($options) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'profileName')->textInput($options)?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'stateName')->textInput($options)?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'PasswordExpirationDate')->textInput($options)?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Cambiar Contrase&ntilde;a</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-3">
                            <div class="form-group float-right">
                                <?=Html::label('&nbsp;','btn-getrandompass', ['style' => 'display: block', 'class' => 'control-label'])?>
                                <?= Html::button('<i class="glyphicon glyphicon-refresh"></i> Generar Contrase&ntilde;a', ['class'=>'btn btn-primary control-input','id'=>'btn-getrandompass'])?>
                            </div>
                        </div>
                        <div class="col-4">
                            <?=$form->field($model, '_password')->widget(PasswordInput::className(), [
                                'language'=>'es_SV',
                                'pluginOptions'=>[
                                    'showMeter'=>TRUE,
                                    'verdictTitles'=> $verdictTitles,
                                ],
                            ]);?>
                        </div>
                        <div class="col-4">
                            <?=$form->field($model, '_passwordconfirm')->widget(PasswordInput::className(), [
                                'language'=>'es_SV',
                                'pluginOptions'=>[
                                    'showMeter'=>FALSE,
                                    'toggleMask'=>FALSE,
                                ],
                            ]);?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', '@web/', ['class'=>'btn btn-danger'])?>
            </span>
        </div>
    </div>
</div>