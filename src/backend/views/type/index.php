<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\State;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$modelName = $searchModel->tableName();
$this->title = 'Tipos';
$this->params['breadcrumbs'][] = $this->title;

$filterState= ArrayHelper::map(State::findAll(['KeyWord'=>'Type']), 'Id', 'Name');
?>
<div class="type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->customFunctions->userCan($modelName.'Create') ? Html::a('<i class="fas fa-plus-circle"></i> Crear Tipo', ['create'], ['class' => 'btn btn-success']):"" ?>
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
                            [
                                'attribute' => 'Id',
                                'headerOptions' => [
                                    'style' => 'width: 8%',
                                ],
                            ],
                            'KeyWord',
                            'Name',
                            'Code',
                            [
                                'attribute' => 'Value',
                                'headerOptions' => [
                                    'style' => 'width: 10%',
                                ],
                            ],
                            'Sort',
                            [
                                'attribute'=>'IdState',
                                'filter'=> Html::activeDropDownList($searchModel, 'IdState', $filterState, ['class'=>'form-control','prompt'=>'--']),
                                'content'=>function($data){
                                    return $data->IdState != 0 ? $data->state->Name:NULL;
                                },
                                'enableSorting'=>TRUE  
                            ],
                            // 'Description',

                            [
                                'class' => kartik\grid\ActionColumn::class,
                                'template' => '{view}&nbsp;&nbsp;{update}&nbsp;|&nbsp;&nbsp;{delete}', // {cancel}
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
