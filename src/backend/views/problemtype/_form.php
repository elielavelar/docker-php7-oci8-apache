<?php

use common\customassets\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Problemtype */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
?>


    <?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-3">
            <?=Html::label( $model->getAttributeLabel('IdActiveType'), $tableName.'-idactivetype')?>
            <?=Html::label( ($model->IdActiveType ? $model->activeType->Name : ''), null, [
                    'class' => 'form-control disabled'
            ])?>
            <?= $form->field($model, 'IdActiveType')->hiddenInput()->label(false) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdComponentType')->dropDownList( $model->getComponentTypes(), []) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdState')->dropDownList( $model->getStates(), []) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 5]) ?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <?= Html::a(
                        Yii::t('app', '{icon} {action}',[
                                'icon' => Html::icon('fas fa-times'),
                                'action' => Yii::t('app', 'Cancel'),
                        ]),
                        ['activetype/view', 'id' => $model->IdActiveType ],
                        ['class' => 'btn btn-danger']) ?>
            </span>
        </div>

    </div>
</div>

    <?php ActiveForm::end(); ?>

