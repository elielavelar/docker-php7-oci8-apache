<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \common\models\Catalog */
/* @var $searchModel \common\models\search\CatalogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Catalogs');
$this->params['breadcrumbs'][] = $this->title;
$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = '';
$template .= $view ? '{view}&nbsp;&nbsp;':'';
$template .= $update ? '{update}&nbsp;&nbsp;':'';
$template .= $delete ? '&nbsp;&nbsp;|&nbsp;&nbsp;{delete}':'';
?>
<div class="catalogs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> '.Yii::t('app', 'Add').' '. Yii::t('app', 'Catalog'), ['create'], ['class' => 'btn btn-success']):"" ?>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            #'Id',
                            'Name',
                            'KeyWord',
                            'Code',
                            [
                                'attribute' => 'IdState',
                                'filter' => $model->getStates(),
                                'content' => function($model){
                                    return $model->IdState ? $model->state->Name:'';
                                }
                            ],
                            //'Description:ntext',
                            [
                                'class' => \kartik\grid\ActionColumn::class,
                                'template' => $template,
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
