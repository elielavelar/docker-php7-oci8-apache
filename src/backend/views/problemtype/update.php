<?php

use common\customassets\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Problemtype */

$this->title = Yii::t('app', '{action} {entity} : {name}',[
        'action' => Yii::t('app', 'Update'),
        'entity' => Yii::t('system', 'Problem Type'),
        'name' =>  $model->Name,
    ]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Incident Category'), 'url' => ['incidentcategory/index']];
$this->params['breadcrumbs'][] = ['label' => ($model->IdActiveType ? $model->activeType->categoryType->Name : ''), 'url' => ['incidentcategory/view', 'id' => ($model->IdActiveType ? $model->activeType->IdCategoryType : '')]];
$this->params['breadcrumbs'][] = Yii::t('system', 'Active Types');
$this->params['breadcrumbs'][] = ['label' => ($model->IdActiveType ? $model->activeType->Name : ''), 'url' => ['activetype/view', 'id' => $model->IdActiveType]];
$this->params['breadcrumbs'][] = Yii::t('system', 'Problem Types');
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
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
                'active' => TRUE
            ],
            [
                'label' => 'Tipos de Solución',
                'content' => $this->render('_form/_detail',[
                    'model'=>$model, 'searchModel'=> $searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                ]),
                'active' => FALSE
            ],
        ]]);
    ?>

</div>
