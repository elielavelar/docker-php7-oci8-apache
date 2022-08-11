<?php
use kartik\grid\GridView;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Setting */
/* @var $searchModel \common\models\search\SettingdetailSearch; */
/* @var $modelDetail backend\models\Settingdetail */
/* @var $dataProvider yii\data\ActiveDataProvider  */

$tableName = $modelDetail->tableName();
$frmName = 'form-'.$tableName;
$controller = 'settingdetail';
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$template = "{update}&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;{delete}";

$url = Yii::$app->getUrlManager()->createUrl('settingdetail');
$dtGrid = 'dt-grid';
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Html::button("<i class='fas fa-plus'></i> Agregar Detalle", ['class'=>'btn btn-success','id'=>'btn-add-detail']); ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                'id'=>$dtGrid,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>true,
                'columns' => [
                    ['class' => \kartik\grid\SerialColumn::class],
                    [
                        'attribute' => 'Sort',
                        'headerOptions' => [
                            'style' => 'width: 5%'
                        ],
                    ],
                    'Name',
                    'Code',
                    [
                        'attribute'=>'IdState',
                        'filter'=> $modelDetail->getStates(),
                        'content'=>function($model){
                            return $model->IdState ? $model->state->Name:NULL;
                        },
                        'enableSorting'=>true  
                    ],
                    [
                        'attribute'=>'IdType',
                        'filter'=> $modelDetail->getTypes(),
                        'content'=>function($model){
                            return $model->IdType ? $model->type->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    'Value',
                    [
                        'class' => \kartik\grid\ActionColumn::class,
                        'template' => $template,
                        'buttons'=>[
                            'update' => function ($url, $model) {
                                return Html::a('<span class="fas fa-edit"></span>', "javascript:editDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Actualizar Detalle'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
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
</div>
<?=$this->render('_modalDetail', ['model'=> $modelDetail])?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $("#btn-add-detail").on('click',function(){
            $("#modal-$tableName").modal();
        });
        
        $("#btn-save-alt").on('click', function(){
            $("#$frmName").submit();
        });
        
        $("#btn-cancel-alt").on('click', function(){
            $("#modal-$tableName").modal("toggle");
        });

        $('#modal-$tableName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#$frmName").on('beforeSubmit',function(){
            var data = new FormData(document.getElementById('$frmName'));
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
                    $("#modal-$tableName").modal("toggle");
                });
            };
            params.ERROR = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$frmName";
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
        frm.ID = "$frmName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idsetting':$model->Id});
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
            frm.ID = "$frmName";
            frm.PREFIX = "$tableName-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
            frm.DATA = data;
            setValuesForm(frm);
            $("#modal-$tableName").modal();
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
