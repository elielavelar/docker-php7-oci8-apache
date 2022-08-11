<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;
use backend\models\Incidentcategory;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentcategory */
/* @var $searchModel \backend\models\IncidentcategorySearch */
/* @var $modelDetail \backend\models\Incidentcategory */
/* @var $dataProvider \yii\data\ActiveDataProvider */
/* @var $searchModelActive \backend\models\ActivetypeSearch */
/* @var $modelActive \backend\models\Activetype */
/* @var $dataProviderActive \yii\data\ActiveDataProvider */

$this->title = 'Actualizar Categoría: '.$model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Categorías de Incidentes', 'url' => ['index']];
if( $model->IdParent ){
    $this->params['breadcrumbs'][] = ['label' => ( $model->IdParent ? $model->parent->Name : ''), 'url' => ['view', 'id' => $model->IdParent]];
}
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$hasChildren = ( $model->IdType ? ( $model->type->Code != Incidentcategory::KEYWORD_CATEGORY ) : false );
?>
<div class="card">
    <div class="card-header">
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
                    'label' => $model->getChildrenLabel(),
                    'content' => $this->render('_form/_children', [
                            'model' => $model,
                            'modelDetail' => $modelDetail,
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                    ]),
                    'visible' => $hasChildren
                ],
                [
                    'label' => 'Tipos de Activo',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 'searchModel'=>$searchModelActive, 'modelDetail'=> $modelActive
                        , 'dataProvider'=>$dataProviderActive,
                        ]),
                    'visible' => $model->IdState && in_array($model->state->Code, [Incidentcategory::STATUS_ACTIVE]),
                    'active' => FALSE
                ],
            ]]);
     ?>
</div>