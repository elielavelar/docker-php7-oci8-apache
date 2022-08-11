<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentdetail */
/* @var $attachmentModel \common\models\Attachment */
/* @var $searchAttachmentModel \common\models\search\AttachmentSearch */
/* @var $attachmentDataProvider \yii\data\ActiveDataProvider */

$this->title = 'Actualizar Detalle: Ticket '.( $model->IdIncident ? $model->incident->Ticket : '')
    .': ' . ($model->IdActivityType ? $model->activityType->Name: '');
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Incidents'), 'url' => ['incident/index']];
$this->params['breadcrumbs'][] = ['label' => ( $model->IdIncident ? $model->incident->Ticket : ''), 'url' => ['incident/update', 'id' => $model->IdIncident]];
$this->params['breadcrumbs'][] = ['label' => ($model->IdActivityType ? $model->activityType->Name: ''), 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => Yii::t('app','General'),
                'content' => $this->render('_form', ['model' => $model]),
                'active' => true
            ],
            [
                'label' => Yii::t('app','Attachments'),
                'content' => $this->render('_form/_attachments',[
                    'modelDetail' => $attachmentModel,
                    'searchModel' => $searchAttachmentModel,
                    'dataProvider' =>  $attachmentDataProvider,
                ]),
            ],
        ]]);
    ?>
</div>
