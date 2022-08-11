<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Servicecentreservice */

$this->title = 'Agregar Servicio';
$this->params['breadcrumbs'][] = ['label' => 'Centros de Servicios', 'url' => ['servicecentre/index']];
$this->params['breadcrumbs'][] = ['label' => $model->serviceCentre->Name, 'url' => ['servicecentre/'.$model->IdServiceCentre]];
$this->params['breadcrumbs'][] = ['label' => 'Servicios'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicecentreservices-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
