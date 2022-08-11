<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\models\Citizen */

$this->title = $model->CompleteName;
$this->params['breadcrumbs'][] = ['label' => 'Ciudadanos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="citizen-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= $model->update ? Html::a('Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']):""; ?>
        <?= $model->delete ? Html::a('Elieminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Está seguro que desea Eliminar este Registro?',
                'method' => 'post',
            ],
        ]):""; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'Name',
            'LastName',
            'Email:email',
            'Telephone',
            'CreateDate',
            'UpdateDate',
            [
                'attribute'=>'IdState',
                'value'=> $model->IdState ? $model->state->Name:"",
            ],
        ],
    ]) ?>
    <?= Html::a('Cancelar', ['index'], ['class'=>'btn btn-danger']); ?>
</div>
