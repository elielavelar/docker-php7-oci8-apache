<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Servicecentreservices */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Servicio', 'url' => ['servicecentre/index']];
$this->params['breadcrumbs'][] = ['label' => $model->serviceCentre->Name, 'url' => ['servicecentre/'.$model->IdServiceCentre]];
$this->params['breadcrumbs'][] = ['label' => 'Servicios'];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicecentreservices-view">

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
                        [
                            'attribute'=>'IdServiceCentre',
                            'value' => $model->IdServiceCentre ? $model->serviceCentre->Name:'',
                        ],
                        'Name',
                        [
                            'attribute'=>'IdType',
                            'value' => $model->IdType ? $model->type->Name:'',
                        ],
                        [
                            'attribute'=>'IdState',
                            'value' => $model->IdState ? $model->state->Name:'',
                        ],
                        'Description:ntext',
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['servicecentre/update','id'=>$model->IdServiceCentre], ['class'=>'btn btn-danger'])?>
</div>
