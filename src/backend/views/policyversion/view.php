<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Policyversions */

$this->title = $model->Version;
$this->title = 'Agregar '.($model->policy->IdType ? $model->policy->type->Name:'');
$this->params['breadcrumbs'][] = ['label' => 'Políticas y Procedimientos', 'url' => ['policy/index']];
$this->params['breadcrumbs'][] = ['label' => $model->policy->Code, 'url' => ['policy/view', 'id' => $model->IdPolicy]];
?>
<div class="policyversions-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->Id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'Id',
            'Version',
            'IdPolicy',
            'IdState',
            'Description:ntext',
            'Approved',
            'Sent',
            'ApprovedDate',
            'SentDate',
            'ActualVersion',
        ],
    ]) ?>

</div>
