<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentdetail */
/* @var $attachmentModel \common\models\Attachment */
/* @var $searchAttachmentModel \common\models\search\AttachmentSearch */
/* @var $attachmentDataProvider \yii\data\ActiveDataProvider */

$this->title = $model->IdActivityType ? $model->activityType->Name : '';
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Incidents')
    , 'url' => ['incident/index']];
$this->params['breadcrumbs'][] = ['label' => ($model->IdIncident ? $model->incident->Ticket : '')
    , 'url' => ['incident/view', 'id' => $model->IdIncident]];
$this->params['breadcrumbs'][] = Yii::t('app','Details');
$this->params['breadcrumbs'][] = $this->title;

$attributes = $model->getAttributesView();
$attachmentModel->disabled = true;

$tabs = [
    [
        'label' => Yii::t('app', 'General'),
        'content' => DetailView::widget([
            'model' => $model,
            'attributes' => $attributes,
        ]),
        'active' => true,
    ]
];
!empty( $attachments ) ? array_push($attachments, [
    'label' => Yii::t('app','Attachments'),
    'content' => $this->render('_form/_attachments',[
        'modelDetail' => $attachmentModel,
        'searchModel' => $searchAttachmentModel,
        'dataProvider' =>  $attachmentDataProvider,
    ]),
]): null;
?>
<div class="incidentdetail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a( '<i class="fas fa-edit"></i> '.Yii::t('app', 'Update'), ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-times"></i> '.Yii::t('app', 'Delete'), ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app','Are you sure you want to delete this record?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="card">
        <div class="card-body">
            <?= Tabs::widget([
                    'items' => $tabs
            ])?>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> '.Yii::t('app', 'Cancel'),['incident/view', 'id' => $model->IdIncident],['class'=> 'btn btn-danger']);?>
</div>
