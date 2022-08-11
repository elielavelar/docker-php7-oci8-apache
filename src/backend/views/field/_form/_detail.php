<?php

use kartik\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Fields */
/* @var $modelDetail common\models\Fieldscatalogs */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchModel \backend\models\FieldscatalogsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$url = \Yii::$app->getUrlManager()->createUrl('fieldcatalog');

$templateDetail = "";
$templateDetail .= $update ? ' {update} ':'';
$templateDetail .= $delete ? ' |&nbsp;&nbsp;&nbsp;{delete} ':'';

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
$dtGrid = $tableName.'-grid';

?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <span class="float-left">
                        <h4 class="card-title">Detalle</h4>
                    </span>
                    <span class="float-right">
                        <?= $create ? Html::button('<i class="fas fa-plus-circle"></i> Agregar', ['type' => 'button','class'=> 'btn btn-success','id' => 'btn-add-detail']): ''; ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?= GridView::widget([
                'id'=>$dtGrid,
                'pjax' => true,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'Sort',
                        'headerOptions' => [
                            'width' => '10%'
                        ],
                    ],
                    'Name',
                    [
                        'attribute'=>'IdState',
                        'filter'=> $modelDetail->getStates(),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                     'Value',
                    [
                        'class' => \kartik\grid\ActionColumn::class,
                        'template' => $templateDetail,
                        'buttons'=>[
                            'update' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['securityincidentdetail/update','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-pencil-alt"></span>', "javascript:editDetail($model->Id)", [
                                            'title' => Yii::t('app', 'Editar Detalle'), 
                                        ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Eliminar Detalle'), 
                                        ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
<?= $this->render('_modalDetail',[
    'model' => $modelDetail
])?>
<?php 
$defaultSort = $modelDetail::SORT_DEFAULT_VALUE;

$js = <<< JS
   $(document).ready(function(){
        $("#btn-add-detail").on('click', function(){
            $("#$modalName").modal();
        });
        
        $("#btn-cancel-alt").on('click', function(){
            $("#$modalName").modal('toggle');
        });
        
        $("#btn-save-alt").on('click', function(){
            $("#$formName").submit();
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

        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        var clearModal = function(){
            var frm = {};
            frm.ID = "$formName";
            var defaultvalues = {};
            $.extend(defaultvalues,{'$tableName-sort':$defaultSort,'$tableName-idfield':$model->Id});
            frm.DEFAULTS = defaultvalues;
            clearForm(frm);
        };
   });
JS;
$this->registerJs($js);

$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
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
            frm.SETBYID = false;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
            frm.DATA = data;
            frm.INPUTEXTRAS = {};
            frm.EXECUTETRIGGER = true;
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
$this->registerJs($script, $this::POS_HEAD);

?>