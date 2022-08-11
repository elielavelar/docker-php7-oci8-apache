<?php

use yii\helpers\Html;
use common\customassets\datatables\DataTables;
use yii\bootstrap4\Tabs;
use backend\models\Incident;

/* @var $this yii\web\View */
/* @var $searchModelAssigned backend\models\IncidentSearch */
/* @var $dataProviderAssigned yii\data\ActiveDataProvider */
/* @var $searchModelInProcess backend\models\IncidentSearch */
/* @var $dataProviderInProcess yii\data\ActiveDataProvider */
/* @var $searchModelSolved backend\models\IncidentSearch */
/* @var $dataProviderSolved yii\data\ActiveDataProvider */
/* @var $searchModelClosed backend\models\IncidentSearch */
/* @var $dataProviderClosed yii\data\ActiveDataProvider */

$this->title = 'Incidencias';
$this->params['breadcrumbs'][] = $this->title;

$update = Yii::$app->customFunctions->userCan('incidentUpdate') ;
$view = Yii::$app->customFunctions->userCan('incidentView') ;

$tableName = $searchModelAssigned->tableName();
$assigned_table = $tableName.'-assigned-table';

$template = '';
$template .= $view  ? '{view} ' : '';
$template .= $update  ? ' {update} ' : '';
$template .= Yii::$app->customFunctions->userCan('incidentDelete')  ? ' | <span style="margin-left:10px"> {delete} </span>' : '';

$attributes =[
    [
        'attribute'=>'Ticket',
        'content'=> function(Incident $model){
            return Html::a($model->Ticket, ['update','id' => $model->Id]);
        },
    ],
        [
            'attribute'=>'TicketDate',
            'content' => function(Incident $model){
                return $model->TicketDate ? Yii::$app->formatter->asDatetime($model->TicketDate, 'php:d-m-Y H:i'):"";
            },
        ],
        [
            'attribute'=>'IncidentDate',
            'content' => function(Incident $model){
                return $model->IncidentDate ? Yii::$app->formatter->asDatetime($model->IncidentDate, 'php:d-m-Y H:i'):"";
            },
        ],
        [
            'attribute' => 'IdServiceCentre',
            'content' => function(Incident $model){
                return $model->IdServiceCentre ? $model->serviceCentre->Name:"";
            },
            'headerOptions' => [
                'width' => '10%'
            ]
        ],
        [
            'attribute' => 'IdCategoryType',
            'content' => function(Incident $model){
                return $model->IdCategoryType ? $model->categoryType->Name:"";
            },
            'headerOptions' => [
                'width' => '200px'
            ]
        ],
        [
            'attribute' => 'IdSubCategoryType',
            'content' => function(Incident $model){
                return $model->IdSubCategoryType ? $model->subCategoryType->Name:"";
            },
            'headerOptions' => [
                'width' => '200px'
            ]
        ],
        [
            'attribute' => 'IdUser',
            'content' => function(Incident $model){
                return $model->IdUser ? $model->user->DisplayName:"";
            },
            'headerOptions' => [
                'width' => '10%'
            ]
        ],
        [
            'attribute' => 'IdState',
            'content' => function(Incident $model){
                return $model->IdState ? $model->state->Name:"";
            },
            'headerOptions' => [
                'width' => '10%'
            ]
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => $template, // {cancel}
            'headerOptions' => [
                'width' => '10%'
            ]
        ]
    ];
?>
<div class="incident-index">
    <p>
        <?= Yii::$app->customFunctions->userCan('incidentCreate') ? Html::a('<i class="fas fa-plus"></i> Crear Incidencia', ['create'], ['class' => 'btn btn-success']):"" ?>
    </p>

    <?= Tabs::widget([
        'items'=> [
            [
                'label'=> 'Asignados',
                'content'=> 
                    Html::tag('div',
                        Html::tag('div',
                            DataTables::widget([
                                'showOnEmpty' => false,
                                'id' => $assigned_table,
                                'dataProvider' => $dataProviderAssigned,
                                'filterModel' => $searchModelAssigned,
                                'clientOptions' => [
                                    'lengthChange' => FALSE,
                                    'pageLength' => 20,
                                    'order' => [
                                        [0, 'desc'],
                                    ]

                                ],
                                'options' => [
                                    'responsive' => true,
                                ],
                                'columns' => $attributes,
                            ])
                        ,['class' => 'card-body'])
                    , ['class' => 'card', 'style' => 'width:100%'])
                ,
                'contentOptions' => ['class' => 'in'],
            ],
            [
                'label'=> 'En Proceso',
                'content'=>
                    Html::tag('div',
                        Html::tag('div',
                            DataTables::widget([
                                'showOnEmpty' => false,
                                'dataProvider' => $dataProviderInProcess,
                                'filterModel' => $searchModelInProcess,
                                'columns' => $attributes,
                                'clientOptions' => [
                                    'lengthChange' => FALSE,
                                    'pageLength' => 20,
                                ],
                            ])
                            ,['class' => 'card-body'])
                        , ['class' => 'card', 'style' => 'width:100%'])
                ,
                //'active' => true,
            ],
            [
                'label'=> 'Solucionados',
                'content'=> Html::tag('div',
                    Html::tag('div',
                        DataTables::widget([
                            'showOnEmpty' => false,
                            'dataProvider' => $dataProviderSolved,
                            'filterModel' => $searchModelSolved,
                            'columns' => $attributes,
                            'clientOptions' => [
                                'lengthChange' => FALSE,
                                'pageLength' => 20,
                            ],
                        ])
                        ,['class' => 'card-body'])
                    , ['class' => 'card', 'style' => 'width:100%'])
            ],
            [
                'label'=> 'Cerrados',
                'content'=> Html::tag('div',
                    Html::tag('div',
                        DataTables::widget([
                            'showOnEmpty' => false,
                            'dataProvider' => $dataProviderClosed,
                            'filterModel' => $searchModelClosed,
                            'columns' => $attributes,
                            'clientOptions' => [
                                'lengthChange' => FALSE,
                                'pageLength' => 20,
                            ],
                        ])
                        ,['class' => 'card-body'])
                    , ['class' => 'card', 'style' => 'width:100%'])
            ],
        ],
    ]); 
?>
</div>
<?php
$script = <<< JS
    $(document).ready( () => {
        $('.kv-table-header').find('th').removeAttr('style');
    });
JS;
$this->registerJs($script);
?>