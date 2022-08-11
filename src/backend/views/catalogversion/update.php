<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model \common\models\Catalogversion */
/* @var $searchModel \common\models\search\CatalogdetailSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $modelDetail \common\models\Catalogdetail */

$this->title = 'Actualizar Versión Catálogo: ' . $model->catalog->Name." ".$model->Version;
$this->params['breadcrumbs'][] = ['label' => 'Catálogos', 'url' => ['catalog/index']];
$this->params['breadcrumbs'][] = ['label' => $model->catalog->Name, 'url' => ['catalog/update','id'=> $model->IdCatalog]];
$this->params['breadcrumbs'][] = 'Versiones';
$this->params['breadcrumbs'][] = ['label' => $model->Version, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualización';
?>
<div class="card">
    <div class="card-header bg-primary">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', ['model' => $model]),
                    'active' => TRUE
                ],
                [
                    'label' => 'Detalles',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                        ]),
                    'active' => FALSE
                ],
            ]]);
     ?>

</div>
