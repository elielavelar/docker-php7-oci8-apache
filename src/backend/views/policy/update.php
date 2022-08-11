<?php

use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Policy */
/* @var $searchModel backend\models\search\PolicyversionSearch */
/* @var $modelDetail backend\models\Policyversion */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Actualización '.($model->IdType ? $model->type->Name:'').': ' . $model->Code;
$this->params['breadcrumbs'][] = ['label' => 'Políticas y Procedimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Code, 'url' => ['view', 'id' => $model->Id]];
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
                    'active' => true
                ],
                [
                    'label' => 'Detalles',
                    'content' => $this->render('_form/_detail',['model'=>$model, 
                        'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,
                        'modelDetail'=>$modelDetail]),
                ],
            ]]);
     ?>

</div>
