<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use trntv\yii\datetime\DateTimeWidget;
use yii\web\JsExpression;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\models\SecurityincidentSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([
    'method' => 'post',
    'id' => $formName,
]); ?>
<div class="row">
    <div class="col-2">
        <?= $form->field($model, 'Year')->dropDownList($model->getYearList(), ['prompt' => '--Seleccione Año--'])?>
    </div>
    <div class="col-3">
        <?= $form->field($model, '_idServiceCentre')->widget(Select2::className(),[
                    'data'=>$model->getServicecentres(),
                    'disabled'=> (!$model->isNewRecord),
                    'initValueText'=> ($model->IdServiceCentre ? $model->serviceCentre->Name:""),
                    'options' => ['placeholder' => '--Seleccione Departamento--'],
                    #'size'=>'lg',
                    'pluginOptions'=> [
                        'allowClear' => true,
                    ],
                'pluginEvents'=> [
                    'change'=> "function(){}",
                ],
        ]);
        ?>
    </div>
    <div class="col-2">
        <?=$form->field($model, 'IncidentDate[]')->widget(DateTimeWidget::className(),[
            'id'=> 'datestart',
            'phpDatetimeFormat' => 'php:d-m-Y',
            'momentDatetimeFormat' => 'DD-MM-YYYY',
            'options'=> [
                'readonly'=> TRUE,
                
            ],
            'clientOptions'=> [
                'locale'=>'ES_es',
                'ignoreReadonly'=> true,
                'showClear' => true,
                #'maxDate'=> new JsExpression('moment()'),
                #'defaultDate' => new JsExpression("moment('$model->IncidentDate','DD-MM-YYYY')"),
            ],
            'clientEvents' => [
                'dp.change' => new JsExpression('function (e) {
                    $("#dateend").data("DateTimePicker").minDate(e.date);
                }'),
            ],
        ])->label('Fecha Inicio')?>
    </div>
    <div class="col-2">
        <?=$form->field($model, 'IncidentDate[]')->widget(DateTimeWidget::className(),[
            'id'=> 'dateend',
            'phpDatetimeFormat' => 'php:d-m-Y',
            'momentDatetimeFormat' => 'DD-MM-YYYY',
            'options'=> [
                'readonly'=> TRUE,
            ],
            'clientOptions'=> [
                'locale'=>'ES_es',
                'ignoreReadonly'=> TRUE,
                'showClear' => true,
                #'maxDate'=> new JsExpression('moment()'),
                #'defaultDate' => new JsExpression("moment('$model->IncidentDate','DD-MM-YYYY')"),
            ],
            'clientEvents' => [
                'dp.change' => new JsExpression('function (e) {
                    $("#datestart").data("DateTimePicker").maxDate(e.date);
                }'),
            ],
        ])->label('Fecha Fin')?>
    </div>
    <div class="col-3">
        <label>&nbsp;</label>
        <span class="float-right">
            <?= Html::button('<i class="fas fa-search"></i> Filtrar', ['class' => 'btn btn-primary','id' => 'btnFilter']) ?>
            <?= Html::button('<i class="fas fa-times"></i> Limpiar', ['class' => 'btn btn-default', 'id'=>'btnReset','type' => 'button']) ?>
        </span>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$script = <<< JS
   $(document).ready(function(){
        
   });
JS;
$this->registerJs($script, View::POS_READY);
?>