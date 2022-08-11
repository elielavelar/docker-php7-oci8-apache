<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincidentdetails */

$this->title = 'Actualizar Detalle Incidencia: ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['securityincident/index']];
$this->params['breadcrumbs'][] = ['label' => 'Incidencia '.$model->securityIncident->Ticket, 'url' => ['securityincident/view', 'id'=> $model->IdSecurityIncident]];
$this->params['breadcrumbs'][] = $model->Title;
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', ['model' => $model]),
                    'active' => true
                ],
                [
                    'label' => 'Adjuntos',
                    'content' => $this->render('_form/_attachments',['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,'modelDetail'=>$modelDetail,]),
                ],
            ]]);
     ?>

</div>
