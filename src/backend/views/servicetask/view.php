<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Servicetask */

$this->title = $model->Name;
$this->title = 'Actualizar Tarea: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Servicio', 'url' => ['servicecentre/index']];
$this->params['breadcrumbs'][] = ['label' => $model->service->serviceCentre->Name, 'url' => ['servicecentre/view','id' => $model->service->IdServiceCentre]];
$this->params['breadcrumbs'][] = 'Servicios';
$this->params['breadcrumbs'][] = ['label' => $model->service->Name, 'url' => ['servicecentreservice/view','id' => $model->IdService]];
$this->params['breadcrumbs'][] = 'Tareas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicetaskcustomstates-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea Eliminar este Registro?',
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
                        'Name',
                        'Host',
                        'Route',
                        'Port',
                        [
                            'attribute' => 'IdProtocolType',
                            'value' => $model->IdProtocolType ? $model->protocolType->Name : '',
                        ],
                        [
                            'attribute' => 'IdState',
                            'value' => $model->IdState ? $model->state->Name : '',
                        ],
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['servicecentreservice/update','id' => $model->IdService], ['class'=>'btn btn-danger'])?>
</div>
