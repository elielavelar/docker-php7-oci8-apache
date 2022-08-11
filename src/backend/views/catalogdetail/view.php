<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetails */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Catalogos', 'url' => ['catalog/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalogVersion->catalog->Name, 'url' => ['catalog/update','id'=>$model->catalogVersion->IdCatalog]];
$this->params['breadcrumbs'][] = 'Versiones';
$this->params['breadcrumbs'][] = ['label' => 'Version '.$model->catalogVersion->Version, 'url' => ['catalogversion/update','id'=>$model->IdCatalogVersion]];
$this->params['breadcrumbs'][] = 'Detalles de Catálogo';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogdetails-view">

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
                            [
                                'attribute'=>'IdCatalogVersion',
                                'value'=> $model->IdCatalogVersion ? $model->catalogVersion->Version:'',
                            ],
                            'KeyWord',
                            'Code',
                            [
                                'attribute'=>'IdState',
                                'value'=> $model->IdState ? $model->state->Name : '',
                            ],
                            [
                                'attribute'=>'IdType',
                                'value'=> $model->IdType ? $model->type->Name: '',
                            ],
                            'Description:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar',['catalogversion/update','id'=>$model->IdCatalogVersion],['class'=>'btn btn-danger'])?>
</div>
