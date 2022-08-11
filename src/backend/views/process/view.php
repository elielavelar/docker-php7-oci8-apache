<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Process */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Processes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete= Yii::$app->customFunctions->userCan($controller.'Delete');

?>
<div class="process-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= $update ? Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']):"";?>
        <?= $delete ? Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Realmente desea Eliminar este Registro?',
                'method' => 'post',
            ],
        ]):""; ?>
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
                                'attribute' => 'IdState',
                                'value' => $model->IdState ? $model->state->Name:'',
                            ],
                            'Description:ntext',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger']);?>
</div>
