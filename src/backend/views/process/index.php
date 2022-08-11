<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\State;
use backend\models\Process;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ProcessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Procesos';
$this->params['breadcrumbs'][] = $this->title;
$controller = Yii::$app->controller->id;

$filterState = ArrayHelper::map(State::findAll(['KeyWord' => StringHelper::basename(Process::class)]),'Id','Name');
?>
<div class="process-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->customFunctions->userCan($controller."Create") ? Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']):"" ?>
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
                            [
                                'attribute' => 'Code',
                                'headerOptions' => [
                                    'style' => 'width: 10%'
                                ],
                            ],
                            [
                                'attribute'=>'IdState',
                                'filter'=> $filterState,
                                'content' => function($model){
                                    return $model->IdState ? $model->state->Name:'';
                                }
                            ],
                            'Description:ntext',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
