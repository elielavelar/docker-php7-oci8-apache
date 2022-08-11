<?php
use kartik\grid\GridView;
use yii\bootstrap4\Html;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* @var $this yii\web\View */
/* @var $model \common\models\Catalog */
/* @var $searchModel \common\models\search\CatalogdetailSearch; */
/* @var $modelDetail common\models\Catalogdetail;  */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$dtGrid = $tableName.'-grid';

$template = "";
$template .= Yii::$app->customFunctions->userCan("catalogdetailView") ? "{view}&nbsp;":"";
$template .= Yii::$app->customFunctions->userCan("catalogdetailUpdate") ? "{edit}&nbsp;{update} ":"";
$template .= Yii::$app->customFunctions->userCan("catalogdetailDelete") ? "&nbsp;&nbsp;|&nbsp;&nbsp;{delete} ":"";

$url = Yii::$app->getUrlManager()->createUrl('catalogdetail');

?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Yii::$app->customFunctions->userCan('catalogCreate') ? 
                        Html::a("<i class='fas fa-plus-circle'></i> Agregar Detalle",['catalogdetail/create','id'=> $model->Id], ['class'=>'btn btn-success','id'=>'btnAddDetail'])
                        :"";
                    ?>
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
                    ['class' => 'yii\grid\SerialColumn'],
                    'Name',
                    [
                        'attribute' => 'KeyWord',
                        'headerOptions' => ['style' => 'width: 20%']
                    ],
                    [
                        'attribute'=>'Code',
                        'headerOptions' => ['style' => 'width:10%'],
                    ],
                    [
                        'attribute'=>'IdType',
                        'filter'=> $modelDetail->getTypes(),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'headerOptions' => ['style' => 'width:10%'],
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdState',
                        'filter'=> $modelDetail->getStates(),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'headerOptions' => ['style' => 'width:10%'],
                        'enableSorting'=>TRUE  
                    ],
                    #'Description',
                    [
                        'class' => \kartik\grid\ActionColumn::class,
                        'template' => $template,
                        'buttons'=>[
                            'update' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['catalogdetail/update','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-edit"></span>', "javascript:editDetail($model->Id);",  [
                                            'title' => Yii::t('app', 'Editar Version'), 
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
<?php
$js = <<< JS
   
JS;
$this->registerJs($js);


$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var editDetail = function(id){
        window.location = '$url/update/'+id;
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
