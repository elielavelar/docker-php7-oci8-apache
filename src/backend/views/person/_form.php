<?php

use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $form yii\widgets\ActiveForm */
/* @var $modelDetail common\models\Personaldocument */
/* @var $searchModel \common\models\PersonaldocumentSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
$tableName = $model->tableName();
$formName = $tableName . '-form';
?>
<?php Pjax::begin([
    'id' => 'pjax-form'
]);?>
<?php
$form = ActiveForm::begin([
        'id' => $formName,
    ]);
#$model->setForm($form);
?>

<div class="card-body">
    <div class='row'>
        <div class='col-4'>
            <?= $form->field($model, 'FirstName')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'SecondName')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'ThirdName')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-4'>
            <?= $form->field($model, 'LastName')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'SecondLastName')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'MarriedName')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-3'>
            <?= $form->field($model, 'Code')->textInput(['maxlength' => true, 'readonly' => true]) ?>
        </div>
        <div class='col-2'>
            <?= $form->field($model, 'IdGenderType')->dropDownList($model->getGenderTypes(), []) ?>
        </div>
        <div class='col-2'>
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
    </div>
    <?=''; #$model->loadDynamicForm();?>
<?php ActiveForm::end(); ?>
    <div class="row">
        <div class="col-12 div-docs">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Documentos Personales</h3>
                    <span class="float-right">
                        <?= Html::button('<i class="fas fa-plus"></i> Agregar', ['type' => 'button', 'class' => 'btn btn-default', 'id' => 'btn-add']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <?=
                    $this->render('_form/' . ($model->isNewRecord ? '_tempdocs' : '_personaldocs'), [
                        'model' => $model, 'modelDetail' => $modelDetail, 'dataProvider' => $dataProvider
                        , 'searchModel' => $searchModel
                    ])
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php Pjax::end();?>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::button('<i class="fas fa-save"></i> Guardar', ['id' => 'btn-save', 'class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'], ['class' => 'btn btn-danger']); ?>
            </span>
        </div>
    </div>
</div>
<?php
$js = <<< JS
   $(document).ready(function(){
        $('#btn-save').on('click', function(){
            $('#$formName').submit();
        });
    });
JS;
$this->registerJs($js);
