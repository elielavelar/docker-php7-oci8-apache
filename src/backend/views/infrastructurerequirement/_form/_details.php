<?php
use yii\helpers\Html;

use common\models\State;
use common\models\Type;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirement */
/* @var $modelDetail backend\models\Infrastructurerequirementdetails */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\InfrastructurerequirementdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= $modelDetail->getRequirementStates();
$filterActivityType= $modelDetail->getActivityTypes();

$urlDetail = \Yii::$app->getUrlManager()->createUrl('infrastructurerequirementdetail');
$gridName = 'dt-grid';
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
                        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar',['infrastructurerequirementdetail/create','id' => $model->Id],['class' => 'btn btn-success']);?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?= GridView::widget([
                    'id'=>$gridName,
                    'pjax' => true,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'Id',
                        'Title',
                        ['attribute'=>'DetailDate',
                            'filterType'=> GridView::FILTER_DATE,
                                    'filterWidgetOptions'=> [
                                        'language'=>'es',
                                        'readonly'=>true,
                                        'pluginOptions'=> [
                                            'format'=>'dd-mm-yyyy',
                                            'autoclose'=>true,
                                            'todayHighlight' => true,
                                        ],
                                    ],
                                    'format' => 'html',
                                    'width'=>'10%',
                        ],
                        [
                            'attribute'=>'IdActivityType',
                            'filter'=> $filterActivityType,
                            'content'=>function($data){
                                return $data->IdActivityType != 0 ? $data->activityType->Name:NULL;
                            },
                            'enableSorting'=>TRUE  
                        ],
                        [
                            'attribute'=>'IdRequirementState',
                            'filter'=> $filterState,
                            'content'=>function($data){
                                return $data->IdRequirementState != 0 ? $data->requirementState->Name:NULL;
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