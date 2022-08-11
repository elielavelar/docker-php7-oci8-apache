<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Holidays */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="card-body">
    <div class="holidays-form">
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <?= $form->field($model, 'IdType')->dropDownList($model->getTypes()) ?>
            </div>
            <div class="col-4">
                <?= $form->field($model, 'IdState')->dropDownList($model->getStates()) ?>
            </div>
            <div class="col-4">
                <?= $form->field($model, 'IdFrequencyType')->dropDownList($model->getFrequencyTypes())?>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?=
                    $form->field($model, 'DateStart')->widget(DatePicker::className(), [
                        'language'=>'es',
                        'readonly'=>TRUE,
                        'options' => ['placeholder' => 'Fecha del Evento...'],
                        'pluginOptions'=>[
                            'format'=>'dd-mm-yyyy',
                            'todayHighlight'=>true,
                            'autoclose'=>true,
                            #'daysOfWeekDisabled' => [0],
                        ],
                    ]);
                ?>
            </div>
            <div class="col-6">
                <?=
                    $form->field($model, 'DateEnd')->widget(DatePicker::className(), [
                        'language'=>'es',
                        'readonly'=>TRUE,
                        'options' => ['placeholder' => 'Fecha del Evento...'],
                        'pluginOptions'=>[
                            'format'=>'dd-mm-yyyy',
                            'todayHighlight'=>true,
                            'autoclose'=>true,
                            #'daysOfWeekDisabled' => [0],
                        ],
                    ]);
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-6">
                <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
            </div>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <div class="form-group float-right">
                <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a('Cancelar', ['index'], ['class'=>'btn btn-danger'])?>
            </div>
        </div>
    </div>
</div>
