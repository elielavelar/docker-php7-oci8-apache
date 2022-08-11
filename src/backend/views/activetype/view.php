<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Activetype */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Categorías de Incidentes', 'url' => ['incidentcategory/index']];
$this->params['breadcrumbs'][] = ['label' => $model->categoryType->Name, 'url' => ['incidentcategory/update/'.$model->IdCategoryType]];
$this->params['breadcrumbs'][] = 'Tipos de Activo';
$this->params['breadcrumbs'][] = $model->Name;
?>
<div class="activetype-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea Eliminar este Registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'Name',
            'IdState',
            'IdCategoryType',
            'Description',
        ],
    ]) ?>
    
    <?= Html::a('Cancelar',['incidentcategory/update','id'=> $model->IdCategoryType],['class'=> 'btn btn-danger'])?>

</div>
