<?php

use yii\bootstrap4\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequest */

$this->title = Yii::t('system', 'Request').' '.$model->Code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Service Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incidentrequest-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('system', '<i class="fas fa-edit"></i> Actualizar'), ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('system', '<i class="fas fa-times"></i> Eliminar'), ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('system', '¿Está seguro que desea Eliminar este Registro?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'Id',
                        'Code',
                        'RequestDate',
                        'IncidentDate',
                        [
                            'attribute' => 'IdTitle',
                            'value' => $model->IdTitle ?
                                $model->title->Title
                                : ''
                        ],
                        [
                            'attribute' => 'IdResource',
                            'value' => $model->IdResource ? ($model->resource->Code.' - '.$model->resource->Name) : ''
                        ],
                        [
                            'attribute' => 'IdCategoryType',
                            'value' => $model->IdCategoryType ? $model->categoryType->Name : ''
                        ],
                        [
                            'attribute' => 'IdSubCategoryType',
                            'value' => $model->IdSubCategoryType ? $model->subCategoryType->Name : ''
                        ],
                        [
                            'attribute' => 'IdServiceCentre',
                            'value' => $model->IdServiceCentre ? $model->serviceCentre->Name : ''
                        ],
                        [
                            'attribute' => 'IdReportUser',
                            'value' => $model->IdReportUser ? $model->reportUser->DisplayName : ''
                        ],

                        'IdPriorityType',
                        'IdUser',
                        'IdApprovedUser',
                        [
                            'attribute' => 'IdState',
                            'value' => $model->IdState ? $model->state->Name : ''
                        ],
                        'TokenId',
                        'Description:ntext',
                        'IdRejectUser',
                        'RejectDate',
                        'ApprovedDate',
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger'])?>
</div>
