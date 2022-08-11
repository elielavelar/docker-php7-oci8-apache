<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use kartik\form\ActiveField;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $model common\models\Appointments */
/* @var $form yii\widgets\ActiveForm */
$tableName = strtolower(StringHelper::basename($model->className()));
$formName = $tableName."-search";

$url = \Yii::$app->getUrlManager()->createUrl('appointment');
?>

<div class="appointments-search">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h4 class="panel-title">Consulta de Disponibilidad</h4>
        </div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'id'=> $formName,
            ]); ?>
            <div class="row">
                <div class="col-md-4">
                   <?= $form->field($model, 'IdServiceCentre')->widget(Select2::className(),[
                        'data'=>$model->getServiceCentres(),
                        'options' => ['placeholder' => '--SELECCIONE DUICENTRO--'],
                        'size'=>'lg',
                        'pluginOptions'=> [
                            'allowClear' => true,
                        ],
                    ]);
                    ?>
                </div>
                <div class="col-md-3">
                    <?=
                        $form->field($model, 'AppointmentDate')->widget(DatePicker::className(), [
                            'language'=>'es',
                            'readonly'=>TRUE,
                            'options' => ['placeholder' => 'Fecha de Cita...'],
                            'pluginOptions'=>[
                                'format'=>'dd-mm-yyyy',
                                'todayHighlight'=>true,
                                'autoclose'=>true,
                                'daysOfWeekDisabled' => [0],
                            ],
                            'pluginEvents'=> [
                                'changeDate'=> "function(e){ validateDate(); }",
                            ],
                        ]);
                    ?>
                </div>
                <div class="col-md-3">
                    <?=
                        $form->field($model, 'AppointmentHour',[
                            'addon' => [
                                'append' => [
                                    'content' => Html::button(Html::tag('i', '', ['class'=>'fa fa-calendar']), ['class'=>'btn btn-sm','id'=>'btn-time']), 
                                    'asButton' => true
                                ],
                            ],
                        ])->textInput(['readonly'=>TRUE]);
                    ?>
                </div>
                <div class="col-md-2">
                    <br>
                    <?= Html::resetButton('Limpiar', ['class' => 'btn btn-default','id'=>'btn-reset']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<div class="modal fade in" id="modal-time" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-sm" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h4 class="modal-title" id="Label"><strong>Seleccionar Hora de Cita</strong></h4>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <h4>*Horarios Disponibles</h4>
                        </div>
                    </div>
                    <div class="row-fluid" id="hours"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
$script = <<< JS
   
$(document).ready(function(){
   $('#btn-time').on('click', function(){
       getAvailableTime();
    });
        
    $('#appointments-appointmenthour').on('focus', function(){
        getAvailableTime();
    });
});
        
var validateDate = function(){
    var frm = {};
    frm.ID = "$formName";
    frm.PREFIX = "$tableName-";
    frm.UPPERCASE = false;
    frm.GETBYNAME = true;
    frm.UNBOUNDNAME = true;
    frm.REPLACE = true;
    frm.REPLACESTRING = {']':''};
    frm.SEPARATORS = ["[","]"];

    var data = getValuesForm(frm);
    var params = {};
    params.URL = "$url/validatedate";
    params.DATA = {'data':JSON.stringify(data)},
    params.METHOD = 'POST';
    params.DATATYPE = 'json';
    params.SUCCESS = function(data){};
    params.ERROR = function(data){
        if(data.errors){
            frm.ERRORS = data.errors;
            setErrorsModel(frm);
        }
    };
    AjaxRequest(params);
};
        
   var getAvailableTime = function(){
    var frm = {};
    frm.ID = "$formName";
    frm.PREFIX = "$tableName-";
    frm.UPPERCASE = false;
    frm.GETBYNAME = true;
    frm.UNBOUNDNAME = true;
    frm.REPLACE = true;
    frm.REPLACESTRING = {']':''};
    frm.SEPARATORS = ["[","]"];

    var data = getValuesForm(frm);
    var params = {};
    params.URL = "$url/gethours";
    params.DATA = {'data':JSON.stringify(data)},
    params.METHOD = 'POST';
    params.DATATYPE = 'json';
    params.SUCCESS = function(data){
        $("#hours").html(data.list);
        $("#modal-time").modal('show');
    };
    params.ERROR = function(data){
        if(data.errors){
            frm.ERRORS = data.errors;
            setErrorsModel(frm);
        }
    };
    AjaxRequest(params);
    $("#$tableName-idservicecentre").focus();
};
    
    var selectHour = function(h){
        $("#$tableName-appointmenthour").val(h).blur();
        $("#modal-time").modal("toggle");
    };
    
JS;
$this->registerJs($script, \yii\web\VIEW::POS_END);
?>