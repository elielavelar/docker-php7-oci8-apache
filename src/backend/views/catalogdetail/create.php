<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetails */

$this->title = 'Agregar Detalle de Catálogo';
$this->params['breadcrumbs'][] = ['label' => 'Catalogos', 'url' => ['catalog/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalogVersion->catalog->Name, 'url' => ['catalog/update','id'=>$model->catalogVersion->IdCatalog]];
$this->params['breadcrumbs'][] = 'Versiones';
$this->params['breadcrumbs'][] = ['label' => 'Version '.$model->catalogVersion->Version, 'url' => ['catalogversion/update','id'=>$model->IdCatalogVersion]];
$this->params['breadcrumbs'][] = 'Detalles de Catálogo';
$this->params['breadcrumbs'][] = 'Agregar';
?>
<div class="card">
    <div class="card-header bg-primary">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
