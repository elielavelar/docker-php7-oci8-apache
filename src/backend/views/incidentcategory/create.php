<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Incidentcaterogy */

$this->title = 'Crear Categoría de Incidente';
$this->params['breadcrumbs'][] = ['label' => 'Categorías de Incidentes', 'url' => ['index']];
if( $model->IdParent ){
    $this->params['breadcrumbs'][] = ['label' => ( $model->IdParent ? $model->parent->Name : ''), 'url' => ['view', 'id' => $model->IdParent]];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
