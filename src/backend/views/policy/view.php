<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Policy */

$this->title = ($model->IdType ? $model->type->Name:'')." ".$model->Code;
$this->params['breadcrumbs'][] = ['label' => 'Políticas y Procedimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="policies-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
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
                            'Code',
                            [
                                'attribute'=>'IdProcess',
                                'value'=> $model->IdProcess ? $model->process->Name:'',
                            ],
                            [
                                'attribute'=> 'IdType',
                                'value' => $model->IdType ? $model->type->Name:'',
                            ],
                            [
                                'attribute'=> 'IdState',
                                'value' => $model->IdState ? $model->state->Name:'',
                            ],
                            'Description:ntext',
                            [
                                'attribute'=>'IdUser',
                                'value'=> $model->IdUser ? $model->user->DisplayName:'',
                            ],
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger']);?>
</div>
