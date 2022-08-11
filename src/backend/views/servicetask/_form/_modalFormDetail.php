<?php

use yii\bootstrap4\Modal;
use kartik\helpers\Html;

/* @var $this \yii\web\View */
/* @var $model \backend\models\Servicetask */
/* @var $modelDetail backend\models\Servicetaskcustomstates */

$url = Yii::$app->getUrlManager()->createUrl('servicetaskcustomstate');
$modalName = "modal-detail";
$formName = 'state-form';
$prefixName = 'servicetaskcustomstate';

?>
<?php Modal::begin([
        'options' => [
            'id' => $modalName,
        ],
        'title' => '<h3>Detalle de Estado</h3>',
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
        $.extend(defaultvalues,{'$prefixName-idservicetask':$model->Id});
        frm.DEFAULTS = defaultvalues;
        frm.EXTRA = function(){
            $('#$prefixName-active').bootstrapSwitch('state', false);
            $("#$prefixName-datestart").kvDatepicker('update');
            $("#$prefixName-dateend").kvDatepicker('update');
        };
        clearForm(frm);
    };
    
   $(document).ready(function(){
        
        $('#$modalName').on('shown.bs.modal', function () {
            $("#$prefixName-datestart").kvDatepicker('update');
            $("#$prefixName-dateend").kvDatepicker('update');
        });
        
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
        $.pjax.reload({container:'#details',async: false});
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
JS;
$this->registerJs($headJS, \yii\web\View::POS_HEAD);
?>