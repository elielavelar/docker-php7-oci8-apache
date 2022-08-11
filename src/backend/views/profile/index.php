<?php

use kartik\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\State;
/* @var $this yii\web\View */
/* @var $model \common\models\Profile */
/* @var $searchModel backend\models\ProfileSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Perfiles';
$this->params['breadcrumbs'][] = $this->title;

/*
$template = '';
$template .= Yii::$app->customFunctions->userCan('profileView')  ? '{view} ' : '';
$template .= Yii::$app->customFunctions->userCan('profileUpdate')  ? ' {update} ' : '';
$template .= Yii::$app->customFunctions->userCan('profileDelete')  ? ' | <span style="margin-left:10px"> {delete} </span>' : '';
 * 
 */
$template = '{view} {update}';

?>
<div class="profile-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('<i class="fa fa-plus-circle"></i> Crear', ['create'], ['class' => 'btn btn-success']) ?>
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
                            'Description',
                            [
                                'attribute'=>'IdState',
                                'filter'=> $model->getStates(),
                                'content'=>function($model){
                                    return $model->IdState ? $model->state->Name:"";
                                },
                            ],

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
