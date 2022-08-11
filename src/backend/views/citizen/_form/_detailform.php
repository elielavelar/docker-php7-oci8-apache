<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use common\models\State;
use kartik\date\DatePicker;
use kartik\form\ActiveField;

/* @var $this yii\web\View */
/* @var $model common\models\Appointments */
/* @var $form yii\widgets\ActiveForm */

$tableName = $model->tableName();
$formName = $tableName."-form";
$url = \Yii::$app->getUrlManager()->createUrl('appointment');
?>
<?php $form = ActiveForm::begin([
    'id'=>$formName,
]); ?>
<div class="panel-body">
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'IdServiceCentre')->dropDownList($model->getServiceCentres(), ['prompt'=>'--SELECCIONE DUICENTRO--']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(), ['prompt'=>'--SELECCIONE TIPO TRAMITE--']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                $form->field($model, 'AppointmentDate')->widget(DatePicker::className(), [
                    'language'=>'es',
                    'readonly'=>TRUE,
                    'options' => [
                        'placeholder' => 'Fecha de Cita...',
                    ],
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
        <div class="col-md-6">
            <?=
                $form->field($model, 'AppointmentHour',[
                    'addon' => [
                        'append' => [
                            'content' => Html::button(Html::tag('i', '', ['class'=>'fa fa-calendar']), ['class'=>'btn btn-sm','id'=>'btn-time']), 
                            'asButton' => true
                        ],
                    ],
                ])->textInput(['readonly'=>TRUE,]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput()->label(FALSE) ?>
    <?= $form->field($model, 'IdCitizen')->hiddenInput()->label(FALSE) ?>
</div>
<?php ActiveForm::end(); ?>
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
   frmData = {};     
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

    /*var data = getValuesForm(frm);*/
    var data = {
        'IdServiceCentre': $("#$tableName-idservicecentre option:selected").val(),
        'AppointmentDate': $("#$tableName-appointmentdate").val()
    };
    var params = {};
    params.URL = "$url/validatedate";
    params.DATA = {'data':JSON.stringify(data)},
    params.METHOD = 'POST';
    params.DATATYPE = 'json';
    params.SUCCESS = function(data){};
    params.ERROR = function(data){
        if(data.errors){
            frm.ERRORS = data.errors;
            setTimeout(function(){setErrorsModel(frm);},500);
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
    frmData = data;    
    var params = {};
    params.URL = "$url/gethours";
    params.DATA = {'data':JSON.stringify(data)},
    params.METHOD = 'POST';
    params.DATATYPE = 'json';
    params.SUCCESS = function(data){
        $("#hours").html(data.list);
        $("#modal-time").modal("show");
    };
    params.ERROR = function(data){
        /*
        if(data.code === 91001){
            frm.ERRORS = {'idservicecentre':data.message};
            setErrorsModel(frm);
        }
        */
        if(data.errors){
            frm.ERRORS = data.errors;
            setErrorsModel(frm);
        }
        
        //swal("Error!", data.message, "error");
        /*swal({
            title: "Error!",
            type: "error",
            text: data.message,
            html: true,
            showCloseButton: true,
        }, function(){
            
        });
        */
        //frm.ERRORS = data.errors;
        //setErrorsModel(data.errors);
    };
    AjaxRequest(params);
    
};
        
var getDetail = function(id){
    var params = {};
    var data = {'id':id};
    params.URL = "$url/get/";
    params.DATA = {'data':JSON.stringify(data)},
    params.METHOD = 'POST';
    params.DATATYPE = 'json';
    params.SUCCESS = function(data){
        var frm = {};
        frm.ID = "$formName";
        frm.PREFIX = "$tableName-";
        frm.UNBOUNDNAME = true;
        frm.MATCHBYNAME = true;
        frm.SEPARATORS = ["[","]"];
        frm.DATA = data;
        setValuesForm(frm);
        swal("Cita Cargada", "Datos de Cita cargados Exitosamente");
    };
    params.ERROR = function(data){
        swal("ERROR "+data.code, data.message, "error");
    };
    AjaxRequest(params);
};
    
    var selectHour = function(h){
        $("#modal-time").modal("hide");
        var frm = {};
        frm.ID = "$formName";
        frm.PREFIX = "$tableName-";
        frm.UNBOUNDNAME = true;
        frm.MATCHBYNAME = true;
        frm.SEPARATORS = ["[","]"];
        frm.DATA = frmData;
        setValuesForm(frm);
        $("#$tableName-appointmenthour").val(h).blur();
        $("#btnRegister").focus();
    };
JS;
$this->registerJs($script, \yii\web\VIEW::POS_END);
?>