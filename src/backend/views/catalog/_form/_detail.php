<?php
use kartik\grid\GridView;
use yii\bootstrap4\Html;
use common\models\Catalogversion;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* @var $this yii\web\View */
/* @var $model \common\models\Catalog */
/* @var $searchModel \common\models\search\CatalogversionSearch */
/* @var $modelDetail \common\models\Catalogversion */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
$dtGrid = $tableName.'-grid';

$template = "";
$template .= Yii::$app->customFunctions->userCan("catalogView") ? "{view}&nbsp;&nbsp;":"";
$template .= Yii::$app->customFunctions->userCan("catalogUpdate") ? "{edit}&nbsp;&nbsp;{update}&nbsp;&nbsp;":"";
$template .= Yii::$app->customFunctions->userCan("catalogDelete") ? " |&nbsp;&nbsp;&nbsp;&nbsp;{delete} ":"";

$url = Yii::$app->getUrlManager()->createUrl('catalogversion');

?>

<div class="card">
    <div class="card-header">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Yii::$app->customFunctions->userCan('catalogCreate') ?
                        Html::button("<i class='fas fa-plus'></i> Agregar Versión", ['class'=>'btn btn-success','id'=>'btn-add-detail'])
                        :"";
                    ?>
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                'id'=>$dtGrid,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>true,
                'columns' => [
                    ['class' => \kartik\grid\SerialColumn::class],
                    'Version',
                    [
                        'attribute' => 'CurrentVersion',
                        'filter' => [
                                Catalogversion::CURRENT_VERSION_DISABLED => Yii::t('app', 'No')
                                , Catalogversion::CURRENT_VERSION_ENABLED => Yii::t('app', 'Yes')
                        ],
                        'content' => function(Catalogversion $model ){
                            return $model->CurrentVersion == Catalogversion::CURRENT_VERSION_ENABLED ?
                                Yii::t('app', 'Yes')
                                : Yii::t('app', 'No');
                        }
                    ],
                    [
                        'attribute'=>'IdState',
                        'filter'=> $modelDetail->getStates(),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    'Description:ntext',
                    [
                        'class' => \kartik\grid\ActionColumn::class,
                        'template' => $template,
                        'buttons'=>[
                            'edit' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-edit"></span>', "javascript:editDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Actualizar Version'), 
                                ]);
                            },
                            'update' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-th-large"></span>', "javascript:updateDetail($model->Id)", [
                                            'title' => Yii::t('app', 'Detalle de Version'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Eliminar Version'), 
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
            </div>
        </div>
    </div>
</div>
<?=$this->render('_modalDetail', ['model'=> $modelDetail])?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $("#btn-add-detail").on('click',function(){
            $("#$modalName").modal();
        });
        
        $("#btn-save-alt").on('click', function(){
            $('#$formName').submit();
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
            params.SUCCESS = function(){
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
        $.extend(defaultvalues,{'$tableName-idcatalog':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
JS;
$this->registerJs($js);


$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var updateDetail = function(id){
        window.location = '$url/update/'+id;
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
            frm.REPLACESTRING = {'[]':'',']':''};
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
JS;
$this->registerJs($script, yii\web\View::POS_HEAD);
?>
