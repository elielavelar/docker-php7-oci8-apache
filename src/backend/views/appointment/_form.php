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
        <div class="col-md-12">
            <?= $form->field($model, 'IdCitizen')->hiddenInput()->label(FALSE) ?>
            <?= $form->field($model, 'citizenName')->textInput(['disabled'=>TRUE]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'IdServiceCentre')->dropDownList($model->getServiceCentres(), []) ?>
        </div>
        <div class="col-md-4">
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
        <div class="col-md-4">
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
        <div class="col-md-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'RegistrationMethodName')->textInput(['disabled'=> TRUE]) ?>
        </div>
    </div>
    
</div>
<div class="panel-footer">
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <span class="pull-right">
                    <?= Html::submitButton($model->isNewRecord ? 'Guardar' : 'Actualizar', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    <?= Html::a('Cancelar',['index'],['class'=>'btn btn-danger']);?>
                </span>
                <span class="pull-left">
                    <?= $model->cancel ? Html::button('Cancelar Cita', ['class'=>'btn btn-warning','id'=>'btnCancel']):""?>
                </span>
            </div>
        </div>
    </div>
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
   $('#btn-time').on('click', function(){
       getAvailableTime();
    });
        
    $('#appointments-appointmenthour').on('focus', function(){
        getAvailableTime();
    });
        
    $("#btnCancel").on('click',function(){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa cancelar la Cita?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, Cancelar!",
            closeOnConfirm: false
        }
        ,function(){
            cancel();
        });
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
    $("#$tableName-idservicecentre").focus();
};
    
    var selectHour = function(h){
        $("#$tableName-appointmenthour").val(h).blur();
        $("#modal-time").modal("toggle");
        $("#btnRegister").focus();
    };
    
    var cancel = function(){
        var data = {id: "$model->Id"};
        var params = {};
        params.URL = "$url/cancel";
        params.DATA = {'data':JSON.stringify(data)};
        params.METHOD = "POST";
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            swal({
                title: "Cita Cancelada",
                type: "success",
                text: data.message,
                html: true,
                showCloseButton: true,
            }, function(){
                window.location = "$url/$model->Id";
            });
        };
        params.ERROR = function(data){
            swal({
                title: "Error!",
                type: "error",
                text: data.message,
                html: true,
                showCloseButton: true,
            }, function(){

            });
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($script, \yii\web\VIEW::POS_END);
?>