<?php

use common\customassets\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequestdetail */
/* @var $attachmentModel \common\models\Attachment */
/* @var $searchAttachmentModel \common\models\search\AttachmentSearch */
/* @var $attachmentDataProvider \yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '{0} {1}', [
        Yii::t('app', 'Detail'),
        ($model->IdActivityType ? $model->activityType->Name : '')
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Service Requests'), 'url' => ['incidentrequest/index']];
$this->params['breadcrumbs'][] = ['label' => ($model->IdIncidentRequest ? $model->incidentRequest->Code : ''), 'url' => ['incidentrequest/view', 'id' => $model->IdIncidentRequest]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incidentrequestdetail-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('system', '<i class=\"fas fa-edit\"></i> Actualizar'), ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('system', '<i class=\"fas fa-times\"></i> Eliminar'), ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('system', '¿Está seguro que desea Eliminar este Registro?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="card">
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => Yii::t('app','General'),
                    'content' => $this->render('_form/_detailview', [
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
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger'])?>
</div>
