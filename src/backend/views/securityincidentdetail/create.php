<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Securityincidentdetails */
$update = Yii::$app->customFunctions->userCan('securityincidentUpdate');
$url = $update ? "update/":"";

$this->title = 'Agregar Detalle de Incidencia de Seguridad';
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['securityincident/index']];
$this->params['breadcrumbs'][] = ['label' => 'Ticket '.$model->securityIncident->Ticket, 'url' => ['securityincident/'.$url.$model->IdSecurityIncident]];
$this->params['breadcrumbs'][] = 'Detalles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
