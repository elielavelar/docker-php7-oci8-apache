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
/* @var $model backend\models\Syslogdetail */
/* @var $form yii\widgets\ActiveForm */
?>
    <?php $form = ActiveForm::begin([
        'id' => 'syslogdetail-form','action'=>['#'],'options'=>[
            'data-pjax' => '',
            'name'=>'syslogdetail-form',
            'enableAjaxValidation' => true,
            ],
    ]); ?>
    <?= Html::activeHiddenInput($model, 'Id');?>
    <?= Html::activeHiddenInput($model, 'IdSysLog');?>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Attribute')->textInput(['maxlength' => true, 'class'=>'form-control','readonly' => TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Value')->textarea(['rows' => 6, 'class'=>'form-control','readonly'=> TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'OldValue')->textarea(['rows' => 6, 'maxlength' => true, 'class'=>'form-control', 'readonly'=> TRUE]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>