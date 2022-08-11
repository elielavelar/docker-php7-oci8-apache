<?php

use yii\helpers\Html;
use kartik\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentcategory */

$this->title = $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Incidentcaterogies', 'url' => ['index']];
if( $model->IdParent ){
    $this->params['breadcrumbs'][] = ['label' => ( $model->IdParent ? $model->parent->Name : ''), 'url' => ['view', 'id' => $model->IdParent]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="incidentcaterogy-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->customFunctions->userCan('incidentcategoryUpdate')
            ? Html::a('<i class="fas fa-edit"></i> '.Yii::t('app', 'Update')
                , ['update', 'id' => $model->Id], ['class' => 'btn btn-primary'])
            :"" ?>
        <?= Yii::$app->customFunctions->userCan('incidentcategoryDelete')
            ? Html::a('<i class="fas fa-times"></i> '.Yii::t('app', 'Delete')
                , ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app','Are you sure you want to delete this record?'),
                'method' => 'post',
            ],
        ]) : ""?>
    </p>
    <div class="card">
        <div class="card-body">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'Id',
                    'Name',
                    'Code',
                    [
                        'attribute'=> 'IdParent',
                        'value'=> $model->IdParent ? $model->parent->Name:"-",
                    ],
                    [
                        'attribute'=> 'IdType',
                        'value'=> $model->IdType ? $model->type->Name:'',
                    ],
                    [
                        'attribute'=> 'IdState',
                        'value'=> $model->IdState ? $model->state->Name:'',
                    ],
                    'Description',
                ],
            ]) ?>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-left"></i> '.Yii::t('app', 'Cancel')
        ,['index'],['class'=> 'btn btn-danger']);?>
</div>
