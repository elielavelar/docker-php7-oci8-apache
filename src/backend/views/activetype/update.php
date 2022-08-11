<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;
use backend\models\Activetype;

/* @var $this yii\web\View */
/* @var $model backend\models\Activetype */
/* @var $searchModel \backend\models\ProblemtypeSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $modelDetail \backend\models\Problemtype */

$this->title = 'Actualizar Tipo de Activo: '.$model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Categorías de Incidentes', 'url' => ['incidentcategory/index']];
$this->params['breadcrumbs'][] = ['label' => $model->categoryType->Name, 'url' => ['incidentcategory/update/'.$model->IdCategoryType]];
$this->params['breadcrumbs'][] = 'Tipos de Activo';
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', ['model' => $model]),
                    'active' => true
                ],
                [
                    'label' => 'Tipos de Problemas',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                        ]),
                    'visible' => $model->IdState ? (in_array($model->state->Code, [Activetype::STATUS_ACTIVE])):false,
                    'active' => false
                ],
            ]]);
     ?>
</div>
