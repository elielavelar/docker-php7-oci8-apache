<?php

use yii\bootstrap4\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidenttitle */

$this->title = $model->Title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Incidenttitles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incidenttitle-view">

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
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'Id',
            'Title',
            'IdCategoryType',
            'Description:ntext',
            'Enabled',
                    ],
                ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger'])?>
</div>
