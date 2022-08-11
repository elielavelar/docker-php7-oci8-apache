<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Settingsdetail */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = ActiveForm::begin([
        'id' => 'settingsdetail-form','action'=>['settingsdetail/save'],'options'=>[
            'data-pjax' => '',
            'name'=>'settingsdetail-form',
            'enableAjaxValidation' => true,
            ],
    ]); ?>
    <?= Html::activeHiddenInput($model, 'Id');?>
    <?= Html::activeHiddenInput($model, 'IdSetting');?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true, 'class'=>'form-control']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'Code')->textInput(['maxlength' => true, 'class'=>'form-control']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'Value')->textInput(['maxlength' => true, 'class'=>'form-control']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'Sort')->input('number',['maxlength'=>true,'class'=>'form-control']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates()) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes()) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'Description')->textarea(['maxlength' => true, 'class'=>'form-control']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>