<?php

use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequestdetail */

$this->title = Yii::t('app', '{action} {entity}', [
        'action' => Yii::t('app', 'Add'),
        'entity' => Yii::t('app', 'Detail'),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Incident Requests'), 'url' => ['incidentrequest/index']];
$this->params['breadcrumbs'][] = ['label' => ( $model->IdIncidentRequest ? $model->incidentRequest->Code : ''), 'url' => ['incidentrequest/view', 'id' => $model->IdIncidentRequest]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incidentrequestdetail-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
