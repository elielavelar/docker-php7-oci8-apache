<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Extendedmodels */
/* @var $form yii\widgets\ActiveForm */
$url = Yii::$app->getUrlManager()->createUrl('extendedmodel');
$tableName = $model->tableName();

$resultJS = <<< JS
    function (params){
        return {
            q: params.term, 
            IdNameSpace: $("#$tableName-idnamespace option:selected").val()
        };
    }
JS;
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class='row'>
        <div class='col-6'>
            <?= Html::label($model->getAttributeLabel('IdRegistredModel'), $tableName . '-idregistredmodel', []) ?>
            <?= Html::label(($model->IdRegistredModel ? $model->keyword : ''), null, ['class' => 'form-control readonly']) ?>
            <?= $form->field($model, 'IdRegistredModel')->hiddenInput(['id' => $tableName . '-idregistredmodel'])->label(false); ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-3'>
            <?=
            $form->field($model, 'IdNameSpace')->widget(Select2::className(), [
                'data' => $model->getNameSpaces(),
                'disabled' => (!$model->isNewRecord),
                'initValueText' => ($model->IdNameSpace ? $model->nameSpace->Name : ""),
                'options' => ['placeholder' => '--SELECCIONE ESPACIO--'],
                #'size'=>'lg',
                'pluginOptions' => [
                    'allowClear' => true,
                ],
                'pluginEvents' => [
                    'change' => "function(){ $('#$tableName-keyword').empty(); }",
                ],
            ])
            ?>
        </div>
        <div class='col-6'>
            <?= Html::label('Ruta', null, []) ?>
            <?= Html::label(($model->IdRegistredModel ? $model->registredModel->CompletePath : ''), null, ['class' => 'form-control readonly']) ?>
        </div>
        <div class="col-3">
            <?=$form->field($model, 'IdState')->dropDownList($model->getStates(), [])?>
        </div>
    </div>
    <div class='row'>
        <div class='col-12'>
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
