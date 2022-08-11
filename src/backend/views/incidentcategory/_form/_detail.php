<?php
use kartik\grid\GridView;
use kartik\helpers\Html;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use backend\models\Incidentcategory;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentcategory */
/* @var $modelDetail backend\models\Activetype */
/* @var $searchModel \backend\models\ActivetypeSearch; */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$controller = 'activetype';
$url = Yii::$app->getUrlManager()->createUrl($controller);
$create = Yii::$app->customFunctions->userCan( $controller.'Create');
$update = Yii::$app->customFunctions->userCan( $controller.'Update');
$delete = Yii::$app->customFunctions->userCan( $controller.'Delete');
$view = Yii::$app->customFunctions->userCan( $controller.'View');

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$dtGrid = $tableName.'-grid';
$modalName = $tableName.'-modal';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-left">
                    <?= $create ?
                        Html::button("<i class='fas fa-plus'></i> Agregar Activo", ['class'=>'btn btn-success','id'=>'btn-add-act'])
                        :"";
                    ?>
                </span>
                <span class="float-right">
                    <?= $create ?
                        $this->render('_modalUploadActive', ['model' => $modelDetail])
                        :"";
                    ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                'id'=> $dtGrid,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>TRUE,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'Name',
                    [
                        'attribute'=>'IdState',
                        'filter'=> $modelDetail->getStates(),
                        'content'=>function($data){
                            return $data->IdState ? $data->state->Name : '';
                        },
                        'enableSorting'=>TRUE  
                    ],
                    'Description',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{edit} {update} | {delete}',
                        'buttons'=>[
                            'edit' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-edit"></span>', "javascript:editActive($model->Id);", [
                                            'title' => Yii::t('app', 'Actualizar Activo'), 
                                ]);
                            },
                            'update' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-th-large"></span>', "javascript:updateActive($model->Id)", [
                                            'title' => Yii::t('app', 'Detalle de Activo'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteActive($model->Id);", [
                                            'title' => Yii::t('app', 'Eliminar Activo'), 
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
<?=$this->render('_modalActiveType', ['model'=> $modelDetail])?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $("#btn-add-act").on('click',function(){
            $("#$modalName").modal();
        });
        
        $("#btn-save-active").on('click',function(){
            $("#$formName").submit();
        });

        $('#$modalName').on('hidden.bs.modal', function () {
            clearModalAct();
        });
        
        $("#$formName").on('beforeSubmit',function(){
           AjaxHttpRequest({
                url: '$url/save',
                formData: true,
                data: new FormData( document.getElementById('$formName')),
                options: {
                    method: 'POST'
                },
                success: ( data ) => {
                    let response = {}
                    if( data.success ){
                        response = {
                            title: 'Registro Guardado!',
                            text: data.title,
                            icon: 'success',
                            button: 'Aceptar'
                        }
                        
                        $('#$modalName').modal('toggle');
                        refreshGridAct();
                    } else {
                        response = {
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            button: 'Aceptar'
                        }
                        
                        setErrorsModel({
                            ID: '$formName',
                            PREFIX: '$tableName-',
                            ERRORS: data.errors
                        });
                    }
                    swal(response)
                }
           })
        }).on('submit', function(e){
            e.preventDefault();
        });
        
    });

    var clearModalAct = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idcategorytype':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
JS;
$this->registerJs($js);


$script = <<< JS
    var refreshGridAct = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var updateActive = function(id){
        window.location = '$url/update/'+id;
    };
        
    var editActive = function(id){
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
        
    var deleteActive = function(id){
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
                refreshGridAct();
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
