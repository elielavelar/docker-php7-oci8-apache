<?php

use yii\bootstrap4\Modal;
use kartik\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \backend\models\Servicecentreservices */
/* @var $modelDetail backend\models\Servicetask */

$url = Yii::$app->getUrlManager()->createUrl('servicetask');
$modalName = "modal-detail";
$formName = 'task-form';
$prefixName = 'servicetask';
$dtGrid = 'dt-grid';
?>
<?php Modal::begin([
        'options' => [
            'id' => $modalName,
        ],
        'title' => '<h3>Detalle de Servicio</h3>',
        'headerOptions' => ['class' => 'bg-primary'],
        'footer' => Html::button('<i class="fas fa-save"></i> Guardar', ['class'=> 'btn btn-success', 'id'=> 'btnSave'])
                ."".Html::button('<i class="fas fa-times-circle"></i> Cerrar', ['class'=> 'btn btn-danger', 'id'=> 'btnClose']),
    ])?>
    <?= $this->render('_formDetail', ['model' => $modelDetail]) ?>
<?php Modal::end();?>
<?php
$js = <<< JS
   
   var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$prefixName-idservice':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
    
   $(document).ready(function(){
        
        $("#modal-detail").on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#btnClose").on('click', function(){
            $("#modal-detail").modal("toggle");
        });
        
        $("#btnSave").on('click', function(){
            $("#$formName").data('yiiActiveForm').submitting = true;
            $("#$formName").yiiActiveForm('validate');
        });
        
        $('#$formName').on('beforeSubmit', function (e) {
            $.ajax({
                url: "$url/save",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data)
                {		
                    //var data = JSON.parse(data);
                    if(data.success == true)
                    {
                        swal({
                            title: data.title,
                            text: data.message,
                            type: "success",
                            showCancelButton: false,
                            confirmButtonColor: "#00A65A",
                            confirmButtonText: "Aceptar",
                            closeOnConfirm: true
                        }, function(){
                            refreshGrid();
                            $("#$modalName").modal("toggle");
                        });
                    }
                    else
                    {
                        swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                        if(data.errors){
                            var errors = {};
                            errors.ID = "$formName";
                            errors.PREFIX = "$prefixName-";
                            errors.ERRORS = data.errors;
                            setErrorsModel(errors);
                        }
                    }
                },
                error: function() 
                {
                    console.log(data);
                } 	        
           });
        }).on('submit', function(e){
            e.preventDefault();
        });
   });
JS;
$this->registerJs($js);

$headJS = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid',async: false});
    };
        
    var editDetail = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formName";
            frm.PREFIX = "$prefixName-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#$modalName").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
    
    var deleteDetail = function(id){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa Eliminar este Registro?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: true
        },
        function(){
            var params = {};
            var data = {'id':id};
            params.URL = "$url/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal("Registro Eliminado", data.message, "warning");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
JS;
$this->registerJs($headJS, \yii\web\View::POS_HEAD);
?>