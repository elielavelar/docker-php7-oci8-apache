<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use common\models\Countries;
use common\models\State;
use yii\web\JsExpression;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\Servicecentre */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$prefix = $tableName;
?>

<div class="servicecentres-form">
    <div class="card-body">
        <div class="row">
            <div class="col-8">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => true,'autocomplete'=>'off']) ?>
            </div>
            <div class="col-4">
                <?= $form->field($model, 'ShortName')->textInput(['autocomplete'=>'off', 'maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-8">
                <?= $form->field($model, 'ServiceName')->textInput(['autocomplete'=>'off', 'maxlength' => true]) ?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'Code')->textInput(['autocomplete'=>'off']) ?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'MBCode')->input('number',['autocomplete'=>'off']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <?= $form->field($model, 'IdCountry')->widget(Select2::className(),[
                                'data'=>$model->getCountries(),
                                'disabled'=> (!$model->isNewRecord),
                                'initValueText'=> ($model->IdCountry ? $model->country->Name:""),
                                'options' => ['placeholder' => '--Seleccione País--'],
                                'size'=> Select2::SIZE_MEDIUM,
                                'pluginOptions'=> [
                                    'allowClear' => true,
                                ],
                            'pluginEvents'=> [
                                'change'=> "function(){ }",
                            ],
                            ]);
                ?>
            </div>
            <div class="col-3">
                <?= $form->field($model, 'IdZone')->dropDownList($model->getZones(),[]); ?>
            </div>
            <div class="col-2">
                <?= $form->field($model, 'IdState')->dropDownList($model->getStates()) ?>
            </div>
            <div class="col-3">
                <?= $form->field($model, 'IdType')->dropDownList($model->getTypes()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <?= $form->field($model, 'Phone')->textInput(['maxlength' => true, 'autocomplete'=>'off']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <?= $form->field($model, 'EnabledMonitoring')->widget(SwitchInput::class,[
                    'options' => [
                        'id' => $prefix.'-enabledmonitoring',
                    ],
                ]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'Address')->textarea(['rows' => 6]) ?>
            </div>
        </div>
        
            
    </div>
</div>
<?php 
$script = <<< JS
    var clearName = function(){
        $("#servicecentres-idcountry").val("");
        $("#servicecentres-namecountry").val("");
    };
        
    $(document).ready(function(){
        $("#servicecentres-namecountry").on('focus',function(){
            clearName();
        });
    });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>