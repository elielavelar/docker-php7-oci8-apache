<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Policy */
/* @var $modelDetail backend\models\Policyversion */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchModel backend\models\search\PolicyversionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$urlDetail = \Yii::$app->getUrlManager()->createUrl('policyversion');

$controller = Yii::$app->controller->id;
$view = Yii::$app->customFunctions->userCan($controller.'View');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$tableName = $modelDetail->tableName();
$dtGrid = $tableName.'-grid';

$templateDetail = "";
$templateDetail .= $view ? ' {view} ':'';
$templateDetail .= $update ? ' {update} ':'';
$templateDetail .= $delete ? ' |&nbsp;&nbsp;&nbsp;{delete} ':'';

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
                        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['policyversion/create','id'=> $model->Id], ['class'=> 'btn btn-success'])?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                    'id'=> $dtGrid,
                    'pjax' => true,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'Version',
                        [
                            'attribute'=>'IdState',
                            'filter'=> $modelDetail->getStates(),
                            'content'=>function($data){
                                return $data->IdState ? $data->state->Name:NULL;
                            },
                            'enableSorting'=>TRUE
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => $templateDetail,
                            'buttons'=>[
                                'view' => function($url, $model){
                                    $urlDetail = \Yii::$app->getUrlManager()->createUrl(['policyversion/'.$model->Id]);
                                    return Html::a('<span class="fas fa-eye"></span>', $urlDetail, [
                                                'title' => Yii::t('app', 'View'),
                                    ]);
                                },
                                'update' => function ($url, $model) {
                                    $urlDetail = \Yii::$app->getUrlManager()->createUrl(['policyversion/update','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-edit"></span>', $urlDetail, [
                                                'title' => Yii::t('app', 'Update'), ]);
                                },
                                'delete' => function ($url, $model) {
                                    #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
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
<?php 
$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
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
            params.URL = "$urlDetail/delete/"+id;
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