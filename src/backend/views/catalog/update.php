<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;
use common\models\Catalog;
/* @var $this yii\web\View */
/* @var $model common\models\Catalog */
/* @var $modelDetail common\models\Catalogversion */
/* @var $searchModel \common\models\search\CatalogversionSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Actualizar Catálogo: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Catálogos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
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
                    'label' => 'Versiones',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                        ]),
                    'visible' => $model->IdState ? (in_array($model->state->Code, [Catalog::STATUS_ACTIVE])):false,
                    'active' => FALSE
                ],
            ]]);
     ?>
</div>