<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Servicecentre */
/* @var $searchModel common\models\search\ServicecentreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','Servicecentres');
$this->params['breadcrumbs'][] = Yii::t('app', 'Administration');
$this->params['breadcrumbs'][] = Yii::t('app', 'Catalogs');
$this->params['breadcrumbs'][] = $this->title;
$controller = Yii::$app->controller->id;
$view = Yii::$app->customFunctions->userCan($controller.'View');
$update = Yii::$app->customFunctions->userCan($controller.'Update');

$tableName = $model->tableName();
$dtGrid = $tableName.'-grid';

$template = '';
$template .= $view ? '{view}': '';
$template .= $update ? '&nbsp;&nbsp;{update}': '';
?>
<div class="servicecentres-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= (Yii::$app->customFunctions->userCan('servicecentreCreate') ? Html::a('<i class="fas fa-plus-circle"></i> Agregar Departamento', ['create'], ['class' => 'btn btn-success']):""); ?>
    </p>
    <div class="card">
        <div class="card-body">
            <?= GridView::widget([
                'id' => $dtGrid,
                'pjax' => true,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    //[
                    //    'attribute' => 'Id',
                    //    'content' => function($model){
                    //        return $model->Id;
                    //    }
                    //],
                    'Name',
                    [
                        'attribute' => 'MBCode',
                        'label' => 'Código MB',
                        'headerOptions' => [
                            'width' => '10%'
                        ],
                    ],
                    [
                        'attribute'=>'IdCountry',
                        'filter'=> $model->getCountries(),
                        'content'=>function($data){
                            return $data->IdCountry != 0 ? $data->country->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdZone',
                        'filter'=> $model->getZones(),
                        'content'=>function($data){
                            return $data->IdZone != 0 ? $data->zone->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdState',
                        'filter'=> $model->getStates(),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdType',
                        'filter'=> $model->getTypes(),
                        'content'=>function($data){
                            return $data->IdType != 0 ? $data->type->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    // 'IdType',
                    // 'Address:ntext',
                    // 'Phone',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => $template,
                    ],
                ],
            ]); ?>
    </div>
</div>
