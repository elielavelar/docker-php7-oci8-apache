<?php
use kartik\grid\GridView;
use kartik\helpers\Html;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentcategory */
/* @var $modelDetail backend\models\Incidentcategory */
/* @var $searchModel \backend\models\IncidentcategorySearch; */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$controller = Yii::$app->controller->id;

$url = Yii::$app->getUrlManager()->createUrl($controller);
$create = Yii::$app->customFunctions->userCan( $controller.'Create');
$update = Yii::$app->customFunctions->userCan( $controller.'Update');
$delete = Yii::$app->customFunctions->userCan( $controller.'Delete');
$view = Yii::$app->customFunctions->userCan( $controller.'View');

$tableName = $modelDetail->tableName();
$formName = $tableName.'-det-form';
$dtGrid = $tableName.'-grid';
$modalName = $tableName.'-modal';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-left">
                    <?= $create ?
                        Html::a("<i class='fas fa-plus'></i> Agregar",['create', 'id' => $model->Id ], ['class'=>'btn btn-success','id'=>'btn-add'])
                        :"";
                    ?>
                </span>
                <span class="float-right">
                    <?= $create ?
                        $this->render('_modalUpload', ['model' => $modelDetail])
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
                            'template' => '{view} {update} | {delete}',
                            'buttons'=>[
                                'update' => function ($url, $model) {
                                    #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-edit"></span>', "javascript:updateChild($model->Id)", [
                                        'title' => Yii::t('app', 'Update'),
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteChild($model->Id);", [
                                        'title' => Yii::t('app', 'Delete'),
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
<?=$this->render('_modalCategory', ['model'=> $modelDetail])?>
<?php
$js = <<< JS
   
JS;
$this->registerJs($js);


$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var updateChild = function(id){
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
