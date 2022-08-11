<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Nullreasons */

$this->title = 'Update Nullreasons: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Nullreasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="nullreasons-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
