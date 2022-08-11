<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PolicySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Políticas y Procedimientos';
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
?>
<div class="policies-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']):'' ?>
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
                            'Code',
                            'Name',
                            [
                                'attribute'=> 'IdProcess',
                                'filter'=> $searchModel->getProcesses(),
                                'content' => function($model){
                                    return $model->IdProcess ? $model->process->Name: '';
                                }
                            ],
                            [
                                'attribute'=>'IdType',
                                'filter' => $searchModel->getTypes(),
                                'content'=> function($model){
                                    return $model->IdType ? $model->type->Name:'';
                                }
                            ],
                            [
                                'attribute'=>'IdState',
                                'filter' => $searchModel->getStates(),
                                'content'=> function($model){
                                    return $model->IdState ? $model->state->Name:'';
                                }
                            ],
                            //'Description:ntext',
                            //'IdUser',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
