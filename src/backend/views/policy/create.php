<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Policy */

$this->title = 'Agregar Política / Procedimiento';
$this->params['breadcrumbs'][] = ['label' => 'Políticas y Procedimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header bg-primary">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
