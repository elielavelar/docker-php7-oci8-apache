<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Incidentdetail */

$this->title = 'Ticket '.$model->incident->Ticket.': Agregar Detalle';
$this->params['breadcrumbs'][] = ['label' => 'Detalle Incidencia', 'url' => ['incident/update', 'id' => $model->IdIncident]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
