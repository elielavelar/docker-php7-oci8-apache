<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Servicetask */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
            'id' => $formName
        ]); ?>
<div class="card-body">
    <div class='row'>
        <div class='col-12'>
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-4'>
            <?= $form->field($model, 'Host')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-2'>
            <?= $form->field($model, 'Port')->input('number',[]) ?>
        </div>
        <div class='col-6'>
            <?= $form->field($model, 'Route')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-4'>
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(), []) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'IdProtocolType')->dropDownList($model->getProtocolTypes(), []) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-6'>
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>
</div>
<?= $form->field($model, 'IdService')->hiddenInput()->label(false) ?>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['servicecentreservice/update','id' => $model->IdService],['class' => 'btn btn-danger'])?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
