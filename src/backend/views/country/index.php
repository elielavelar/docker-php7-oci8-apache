<?php

use yii\bootstrap4\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model \common\models\Country */
/* @var $searchModel \common\models\search\CountrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Países';
$this->params['breadcrumbs'][] = $this->title;
$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = '';
$template .= $view ? '{view}':'';
$template .= $update ? '&nbsp;&nbsp;{update}':'';
$template .= $view ? '&nbsp;&nbsp;|&nbsp;{delete}':'';
?>
<div class="countries-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php #echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']):"" ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => \kartik\grid\SerialColumn::class],

                            'Id',
                            'Name',
                            'Code',
                            [
                                'attribute'=>'IdState',
                                'filter'=> $model->getStates(),
                                'content'=>function($data){
                                    return $data->IdState ? $data->state->Name : null;
                                },
                                'enableSorting'=> true
                            ],
                            [
                                'class' => kartik\grid\ActionColumn::class,
                                'template' => $template,
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
