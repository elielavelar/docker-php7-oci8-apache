<?php

use yii\bootstrap4\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogversion */

$this->title = $model->catalog->Name." ".$model->Version;
$this->params['breadcrumbs'][] = ['label' => 'Catálogos', 'url' => ['catalog/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalog->Name, 'url' => ['catalog/update','id'=> $model->IdCatalog]];
$this->params['breadcrumbs'][] = 'Versiones';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogversion-view">

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
                            'Version',
                            [
                                'attribute' => 'IdCatalog',
                                'value'=> $model->catalog->Name
                            ],
                            [
                                'attribute'=> 'IdState',
                                'value'=> $model->IdState ? $model->state->Name:'',
                            ],
                            'Description:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['catalog/update','id'=> $model->IdCatalog], ['class'=>'btn btn-danger']);?>
</div>
