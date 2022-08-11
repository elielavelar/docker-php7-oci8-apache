<?php

use yii\helpers\Html;
#use yii\widgets\DetailView;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Holidays */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Días Feriados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="holidays-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Está seguro que desea Eliminar el Registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'Name',
            'Description:ntext',
            [
                'attribute'=>'IdType',
                'value'=> $model->IdType ? $model->type->Name:"",
            ],
            [
                'attribute'=>'IdFrequencyType',
                'value'=> $model->IdFrequencyType ? $model->frequencyType->Name:"",
            ],
            [
                'attribute'=>'IdState',
                'value'=> $model->IdState ? $model->state->Name: "",
            ],
            'DateStart',
            'DateEnd',
            
        ],
    ]) ?>
    <?= Html::a('Cancelar', ['index'], ['class'=>'btn btn-danger'])?>

</div>
