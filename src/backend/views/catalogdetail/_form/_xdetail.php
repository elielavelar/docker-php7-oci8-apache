<?php
use kartik\grid\GridView;
use yii\bootstrap4\Html;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* @var $this yii\web\View */
/* @var $model \common\models\Catalogdetail */
/* @var $searchModel \common\models\search\CatalogdetailvalueSearch */
/* @var $modelDetail common\models\Catalogdetailvalue; */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$dtGrid = $tableName.'-grid';
$modalName = $tableName.'-modal';

$create = Yii::$app->customFunctions->userCan("catalogdetailCreate");
$view = Yii::$app->customFunctions->userCan("catalogdetailView");
$update = Yii::$app->customFunctions->userCan("catalogdetailUpdate");
$delete = Yii::$app->customFunctions->userCan("catalogdetailDelete");

$template = "";
$template .= $update ? "{edit}&nbsp;&nbsp;":"";
$template .= $delete ? "|&nbsp;&nbsp;{delete} ":"";

$url = Yii::$app->getUrlManager()->createUrl('catalogdetailvalue');

?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= $create ? Html::button("<i class='fas fa-plus-circle'></i> Agregar Valor", ['class'=>'btn btn-success','id'=>'btn-add-detail']):""; ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                'id'=> $dtGrid,
                'pjax' => true,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>TRUE,
                'columns' => [
                    ['class' => 'kartik\grid\SerialColumn'],
                    [
                        'attribute'=>'Sort',
                        'headerOptions' => ['style' => 'width:10%'],
                    ],
                    [
                        'attribute'=>'IdDataType',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdDataType', $modelDetail->getDataTypes(), ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdDataType != 0 ? $data->dataType->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdValueType',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdValueType', $modelDetail->getValueTypes(), ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdValueType != 0 ? $data->valueType->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    'Value',
                    'Description:ntext',
                    [
                        'class' => \kartik\grid\ActionColumn::class,
                        'template' => $template,
                        'buttons'=>[
                            'edit' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-pencil-alt"></span>', "javascript:editDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Editar Valor'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Eliminar Valor'), 
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
        $.extend(defaultvalues,{'$tableName-idcatalogdetail':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
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
