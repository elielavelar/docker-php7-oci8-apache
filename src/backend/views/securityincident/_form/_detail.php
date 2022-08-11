<?php

use yii\helpers\Html;

use backend\models\Securityincident;
use backend\models\Securityincidentdetails;
use common\models\State;
use common\models\Type;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */
/* @var $modelDetail backend\models\Securityincidentdetails */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\SecurityincidentdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= ArrayHelper::map(State::find()->where(['KeyWord'=> StringHelper::basename(Securityincident::class)])->orderBy(['Value' => SORT_ASC])->all(), 'Id', 'Name');
$filterType= ArrayHelper::map(Type::find()->where(['KeyWord'=>StringHelper::basename(Securityincidentdetails::class)."Activity"])->orderBy(['Value' => SORT_ASC])->all(), 'Id', 'Name');
$urlDetail = \Yii::$app->getUrlManager()->createUrl('securityincidentdetail');

$templateDetail = "";
$templateDetail .= $view ? ' {view} ':'';
$templateDetail .= ($admin || $update) ? ' {update} ':'';
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
                    <?php if($update):?>
                    <span class="float-right">
                        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['securityincidentdetail/create','id'=> $model->Id], ['class'=> 'btn btn-success'])?>
                    </span>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?php Pjax::begin([
                    'id'=>'details'
                ]); ?>    
                <?= GridView::widget([
                'id'=>'dtgrid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    'Title',
                    [
                        'attribute'=>'IdIncidentState',
                        'filter'=> $modelDetail->getIncidentStates(),
                        'content'=>function($data){
                            return $data->IdIncidentState != 0 ? $data->incidentState->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdActivityType',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdActivityType', $filterType, ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdActivityType != 0 ? $data->activityType->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                     'Commentaries:ntext',
                     'Investigation:ntext',
                     'KnowledgeBase:ntext',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => $templateDetail,
                        'buttons'=>[
                            'view' => function($url, $model){
                                $urlDetail = \Yii::$app->getUrlManager()->createUrl(['securityincidentdetail/'.$model->Id]);
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $urlDetail, [
                                            'title' => Yii::t('app', 'lead-delete'), 
                                ]);
                            },
                            'update' => function ($url, $model) {
                                $urlDetail = \Yii::$app->getUrlManager()->createUrl(['securityincidentdetail/update','id'=>$model->Id]);
                                return ( ($model->IdActivityType ? $model->activityType->Code != Securityincidentdetails::ACTIVITY_ASSIGNMENT:FALSE) ?  Html::a('<span class="glyphicon glyphicon-pencil"></span>', $urlDetail, [
                                            'title' => Yii::t('app', 'lead-edit'), 
                                ]) : '');
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return ( ($model->IdActivityType ? $model->activityType->Code != Securityincidentdetails::ACTIVITY_ASSIGNMENT:FALSE) ? Html::a('<span class="glyphicon glyphicon-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                            'title' => Yii::t('app', 'lead-delete'), 
                                ]) : '');
                            },
                        ],
                          
                         
                    ],
                ],
            ]); ?>
        <?php Pjax::end(); ?></div>
        </div>
    </div>
</div>
<?php 
$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#details',async: false});
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