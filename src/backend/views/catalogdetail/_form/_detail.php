<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetail */
/* @var $modelDetail common\models\Catalogdetailvalue */

$this->title = 'Detalle de Valores';
$this->params['breadcrumbs'][] = $this->title;
$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
$controller = Yii::$app->controller->id;
$url =  \Yii::$app->getUrlManager()->createUrl($controller.'value');

$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');

?>
<div class="catalogdetailvalues-index">
    <table class="table table-bordered table-hover" id="detail-list">
        <thead>
            <tr>
                <th colspan="6">
                    <span class="float-right">
                        <?= $create ? Html::button('<i class="fas fa-plus"></i> Agregar Valor', ['class' => 'btn btn-success','id'=>'btn-add']) : "";?>
                    </span>
                </th>
            </tr>
            <tr class="bg-primary">
                <th style="width:5%"><?=$modelDetail->getAttributeLabel('Id')?></th>
                <th style="width:4%"><?=$modelDetail->getAttributeLabel('Sort')?></th>
                <th style="width:20%"><?=$modelDetail->getAttributeLabel('IdDataType')?></th>
                <th style="width:20%"><?=$modelDetail->getAttributeLabel('IdValueType')?></th>
                <th style="width:40%"><?=$modelDetail->getAttributeLabel('Value')?></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?=$modelDetail->htmlList; ?>
        </tbody>
    </table>
</div>
<?=$this->render('_modalDetail', ['model'=> $modelDetail])?>
<?php
$js = <<< JS
        
    $(document).ready(function(){

        $("#btn-add").on('click', function(){
            $("#$modalName").modal();
        });

        $("#btn-save-alt").on('click', function(){
            $("#$formName").submit();
        });
        
        $("#btn-cancel-alt").on('click', function(){
            $("#$modalName").modal("toggle");
        });
        
        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#$formName").on('beforeSubmit',function(){
            var data = new FormData(document.getElementById('$formName'));
            var params = {};
            params.URL = '$url/save';
            params.DATA = data;
            params.DATATYPE = 'json';
            params.METHOD = 'POST';
            params.CACHE = false;
            params.PROCESSDATA = false;
            params.CONTENTTYPE = false;
            params.SUCCESS = function(data){
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
            };
            params.ERROR = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$formName";
                    errors.PREFIX = "$tableName-";
                    errors.ERRORS = data.errors;
                    errors.EXTRA = function(){};
                    setErrorsModel(errors);
                }
            };
            AjaxRequest(params);
        }).on('submit', function(e){
            e.preventDefault();
        });
    });

    var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {'$tableName-idcatalogdetail': $model->Id };
        frm.EXTRA = function(){
        };
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
    
JS;
$this->registerJs($js);

$script = <<< SC
    var refreshGrid = function(){
        var params = {};
        params.URL = "$url/gethtmllist";
        params.DATA = {};
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.PROCESSDATA = false;
        params.CONTENTTYPE = false;
        params.CACHE = false;
        params.SUCCESS = function(data){
            $("#detail-list").find("tbody")
                .empty()
                .html(data.list);
        };
        params.ERROR = function(data){};
        AjaxRequest(params);
    };
        
    var addDetail = function(id){
        $("#$tableName-idparentvalue").val(id);
        $("#$modalName").modal();
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
            frm.PREFIX = "$tableName-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
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
            var data = {'Id':id};
            params.URL = "$url/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal(data.title, data.message, "warning");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
SC;
$this->registerJs($script, yii\web\View::POS_HEAD);
?>