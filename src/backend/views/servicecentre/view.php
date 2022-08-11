<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Servicecentres */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Centros de Atención', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicecentres-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
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
                            'ShortName',
                            'Code',
                            'MBCode',
                            [
                                'attribute'=>'IdCountry',
                                'value'=> $model->IdCountry ? $model->country->Name:NULL,
                            ],
                            [
                                'attribute'=>'IdZone',
                                'value'=> $model->IdZone ? $model->zone->Name:NULL,
                            ],
                            [
                                'attribute'=>'IdState',
                                'value'=> $model->IdState ? $model->state->Name:NULL,
                            ],
                            [
                                'attribute'=>'IdType',
                                'value'=> $model->IdType ? $model->type->Name:NULL,
                            ],
                            'Address:ntext',
                            'Phone',
                            [
                                'attribute' => 'EnabledMonitoring',
                                'value' => $model->EnabledMonitoring == $model::MONITORING_ENABLED ?  'Sí': 'No',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Atras', ['index'], ['class'=>'btn btn-danger']); ?>
</div>
