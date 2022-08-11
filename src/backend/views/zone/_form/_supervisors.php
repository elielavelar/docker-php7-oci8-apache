<?php
use yii\helpers\Html;

use common\models\State;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model \common\models\Zones */
/* @var $modelDetail common\models\Zonesupervisors */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\ZonesupervisorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= $modelDetail->getStates();

$url = \Yii::$app->getUrlManager()->createUrl('zonesupervisor');
$gridName = 'dt-grid';
$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';

$Name = $model->Name;
?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <span class="float-left">
                        <h4 class="card-title">Supervisores</h4>
                    </span>
                    <span class="float-right">
                        <?= Html::button('<i class="fas fa-plus-circle"></i> Agregar',['type' => 'button','class' => 'btn btn-success','id' => 'btnAddDetail']);?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'id'=>$gridName,
                        'pjax' => true,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            #'Id',
                            [
                                'attribute' => 'IdUser',
                                'content' => function($model){
                                    return $model->IdUser ? $model->user->DisplayName : '';
                                }
                            ],
                            [
                                'attribute'=>'IdState',
                                'filter'=> $filterState,
                                'content'=>function($data){
                                    return $data->IdState != 0 ? $data->state->Name:NULL;
                                },
                                'enableSorting'=>TRUE  
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{edit} {delete}',
                                'buttons'=>[
                                    'edit' => function ($url, $model) {
                                        #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', "javascript:getDetail($model->Id);", [
                                                    'title' => Yii::t('app', 'lead-edit'), 
                                        ]);
                                    },
                                    'delete' => function ($url, $model) {
                                        #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                                    'title' => Yii::t('app', 'lead-delete'), 
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_modalSupervisor', [
    'model' => $modelDetail, 'formName' => $formName, 'tableName' => $tableName,
    'modalName' => $modalName, 
])?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $('#btnAddDetail').on('click', function(){
            $("#$modalName").modal();
        });
        
        $('#btnDetCancel').on('click', function(){
            $("#$modalName").modal('toggle');
        });
        
        $("#btnDetSave").on('click', function(){
            swal({
                title: "Confirmación?",
                text: "¿Está seguro que desesa Registrar este Supervisor en la $Name?",
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
JS;
$this->registerJs($js, $this::POS_READY);

$jsHead = <<< JS
    
    var getDetail = function(id){
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
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            frm.EXTRA = function(){
                $('#$tableName-idmember').trigger('change').attr('disabled', true);
            };
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
            text: "¿Está seguro que desesa Desvincular este Supervisor de la $Name?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: true
        },
        function(){
            var params = {};
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
        
    var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idzone':$model->Id});
        frm.DEFAULTS = defaultvalues;
        frm.EXTRA = function(){
            $('#$tableName-iduser').val('').trigger('change');
        };
        clearForm(frm);
    };
    
    var refreshGrid = function(){
        $.pjax.reload({container:'#$gridName-pjax'});
    };
JS;
$this->registerJs($jsHead, $this::POS_HEAD);
?>