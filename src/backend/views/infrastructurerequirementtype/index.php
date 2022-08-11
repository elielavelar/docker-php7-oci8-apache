<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirementtype */

$this->title = 'Tipos de Requerimientos';
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$filterState = $model->getStates();
$filterServicecentre = $model->getServiceCentres();

$create = Yii::$app->customFunctions->userCan($controller . 'Create');
$update = Yii::$app->customFunctions->userCan($controller . 'Update');
$delete = Yii::$app->customFunctions->userCan($controller . 'Delete');
$view = Yii::$app->customFunctions->userCan($controller . 'View');

$url =  \Yii::$app->getUrlManager()->createUrl($controller);

$template = "";
$template .= $view ? '{view}&nbsp;' : '';
$template .= $update ? '{update}&nbsp;' : '';
$template .= $delete ? 'nbsp;|nbsp;nbsp;nbsp;{delete}' : '';

$tableName = $model->tableName();
$modalName = 'modal-detail';
$formName = 'form-detail';
$gridName = $tableName."-grid";

?>
<div class="infrastructurerequirement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered table-hover" id="<?=$gridName?>">
                        <thead>
                            <tr>
                                <th colspan="7">
                                    <span class="float-right">
                                        <?= $create ? Html::button('<i class="fas fa-plus-circle"></i> Crear Tipo', ['class' => 'btn btn-success','id'=>'btnAdd']) : "";?>
                                    </span>
                                </th>
                            </tr>
                            <tr class="bg-primary">
                                <th style="width:8%"><?=$model->getAttributeLabel('Id')?></th>
                                <th style="width:30%"><?=$model->getAttributeLabel('Name')?></th>
                                <th style="width:12%"><?=$model->getAttributeLabel('Code')?></th>
                                <th style="width:20%"><?=$model->getAttributeLabel('IdParent')?></th>
                                <th style="width:12%"><?=$model->getAttributeLabel('IdServiceCentre')?></th>
                                <th style="width:8%"><?=$model->getAttributeLabel('IdState')?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?=$model->htmlList; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_form/_modalDetail', [
    'model' => $model,
    'formName' => $formName, 'modalName' => $modalName, 'tableName' => $tableName,
])?>
<?php 
$js = <<< JS
        
    $(document).ready(function(){

        $("#btnAdd").on('click', function(){
            $("#$modalName").modal();
        });

        $("#btnCancel").on('click', function(){
            $("#$modalName").modal("toggle");
        });

        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        
        $("#btnDetSave").on('click', function(){
            swal({
                title: "Confirmación?",
                text: "¿Está seguro que desesa Guardar este Tipo de Requerimiento?",
                type: "success",
                showCancelButton: true,
                confirmButtonColor: "#008d4c",
                confirmButtonText: "Guardar",
                closeOnConfirm: true
            },
            function(){
                $("#$formName").submit();
            });
        });
        
        $("#btnDetCancel").on('click', function(){
            $("#$modalName").modal('toggle');
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
                swal(data.title, data.message, "success");
                $("#$modalName").modal("toggle");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$formName";
                    errors.PREFIX = "$tableName-";
                    errors.ERRORS = data.errors;
                    errors.EXTRA = function(){
                        
                    };
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
        var defaultvalues = {};
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
            $("#$gridName").find("tbody")
                .empty()
                .html(data.list);
        };
        params.ERROR = function(data){};
        AjaxRequest(params);
    };
        
    var addDetail = function(id){
        $("#$tableName-idparent").val(id);
        $("#$modalName").modal();
    };
        
    var editDetail = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get";
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
            var data = {};
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