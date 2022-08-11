<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Incident */

$this->title = "Ticket # ".$model->Ticket;
$this->params['breadcrumbs'][] = ['label' => 'Incidencias', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incident-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->customFunctions->userCan('incidentUpdate') ? Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']):""; ?>
        <?= Yii::$app->customFunctions->userCan('incidentDelete') ? Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea eliminar este Registro?',
                'method' => 'post',
            ],
        ]):"" ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'mode' => DetailView::MODE_VIEW,
        'enableEditMode' => false,
        'attributes' => [
            [
                'columns' => [
                    'Id',
                    'Ticket',
                ]
            ],
            [
                    'columns' => [
                        [
                            'attribute' => 'IncidentDate',
                            'valueColOptions' => ['style' => 'width: 13%']
                        ],
                        [
                            'attribute' => 'TicketDate',
                            'valueColOptions' => ['style' => 'width: 13%']
                        ],
                        [
                            'attribute' => 'SolutionDate',
                            'valueColOptions' => ['style' => 'width: 14%']
                        ],
                    ]
            ],
            [
                    'columns' => [
                        [
                            'attribute'=>'IdServiceCentre',
                            'value'=> $model->IdServiceCentre ? $model->serviceCentre->Name : '',
                            'valueColOptions' => ['style' => 'width: 30%']
                        ],
                        [
                            'attribute'=>'IdReportUser',
                            'value'=> $model->IdReportUser ? $model->reportUser->DisplayName : '',
                            'valueColOptions'=>['style'=>'width:30%']
                        ],
                    ]
            ],
            [
                    'columns' => [
                        [
                            'attribute'=> 'IdCategoryType',
                            'value'=> $model->IdCategoryType ? $model->categoryType->Name : '',
                        ],
                        [
                            'attribute'=> 'IdSubCategoryType',
                            'value'=> $model->IdSubCategoryType ? $model->subCategoryType->Name : '',
                        ],
                    ]
            ],
            [
                'attribute'=> 'IdInterruptType',
                'value'=> $model->IdInterruptType ? $model->interruptType->Name : '',
            ],
            'InterruptDate',
            [
                'attribute'=> 'IdPriorityType',
                'value'=> $model->IdPriorityType ? $model->priorityType->Name : '',
            ],
            [
                'attribute'=>'IdRevisionType',
                'value'=> $model->IdRevisionType ? $model->revisionType->Name : '',
            ],
            [
                'attribute'=> 'IdState',
                'value'=> $model->IdState ? $model->state->Name : ''
            ],
            'Commentaries',
            [ 
                'attribute'=> 'IdUser',
                'value'=> $model->IdUser ? $model->user->DisplayName : '',
            ],
            [ 
                'attribute'=> 'IdCreateUser',
                'value'=> $model->IdCreateUser ? $model->createUser->DisplayName: null,
            ],
            [
                'attribute'=> 'IdParentIncident',
                'value'=> $model->IdParentIncident ? $model->parentIncident->Ticket: null,
            ],
        ],
    ]) ?>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> '.Yii::t('app', 'Cancel'),['index'],['class'=> 'btn btn-danger']);?>
</div>
