<?php

use common\customassets\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequestdetail */
/* @var $attachmentModel \common\models\Attachment */
/* @var $searchAttachmentModel \common\models\search\AttachmentSearch */
/* @var $attachmentDataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{action} {entity}', [
    'action' => Yii::t('app', 'Update'),
    'entity' => Yii::t('app', 'Detail'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Service Requests'), 'url' => ['incidentrequest/index']];
$this->params['breadcrumbs'][] = ['label' => ( $model->IdIncidentRequest ? $model->incidentRequest->Code : ''), 'url' => ['incidentrequest/view', 'id' => $model->IdIncidentRequest]];
$this->params['breadcrumbs'][] = [
    'label' => ($this->title.': '. ($model->IdActivityType ? $model->activityType->Name : ''))
    , 'url' => ['view', 'id' => $model->Id]
];
?>
<div class="incidentrequestdetail-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app','General'),
                    'content' => $this->render('_form', [
                        'model' => $model
                    ]),
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
</div>

