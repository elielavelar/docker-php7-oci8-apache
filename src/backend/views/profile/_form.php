<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Profile */
/* @var $form yii\widgets\ActiveForm */

$options = [
    'maxlength'=>TRUE,
    'disabled'=>$model->disabled,
];
$addoptions = [];
if(!$model->isNewRecord && !$model->disabled){
    $addoptions['disabled']= TRUE;
}
$addoptions = array_merge($options, $addoptions);
?>

<div class="card-body">
    <div class="row">
        <div class="col-8">
            <?= $form->field($model, 'Name')->textInput($options) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'KeyWord')->textInput($addoptions) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'Code')->textInput($addoptions) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),$options) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea($options) ?>
        </div>
    </div>
</div>
