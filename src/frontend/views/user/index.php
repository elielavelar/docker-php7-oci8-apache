<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use common\models\State;
use common\models\Profile;
use common\models\Servicecentres;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;

$create = Yii::$app->customFunctions->userCan('userCreate');
$update = Yii::$app->customFunctions->userCan('userUpdate');
$delete = Yii::$app->customFunctions->userCan('userDelete');
$view = Yii::$app->customFunctions->userCan('userView');

$template = '';
$template .= $view ? '{view}&nbsp;&nbsp;':'';
$template .= $update ? '{update}&nbsp;&nbsp;':'';
$template .= $delete ? '&nbsp;|&nbsp;&nbsp;&nbsp;{delete}':'';

?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> Crear Usuario', ['create'], ['class' => 'btn btn-success']):"" ; ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'columns' => [
                            //['class' => 'yii\grid\SerialColumn'],
                            //'id',
                            [
                                'attribute' => 'Username',
                                'label' => 'Usuario',
                                'headerOptions' => ['style' => 'width:8%'],
                            ],
                            [
                                'attribute'=>'DisplayName',
                                'headerOptions' => ['style' => 'width:15%'],
                            ],
                             'Email:email',
                            [
                                'attribute'=>'IdProfile',
                                'filter'=> $searchModel->getProfiles(),
                                'content'=>function($model){
                                    return $model->IdProfile ? $model->profile->Name:"";
                                },
                                'headerOptions' => ['style' => 'width:12%'],
                            ],
                            [
                                'attribute'=>'IdServiceCentre',
                                'filter'=> $searchModel->getServiceCentres(),
                                'content'=>function($model){
                                    return $model->IdServiceCentre ? $model->serviceCentre->Name:"";
                                },
                                'headerOptions' => ['style' => 'width:8%'],
                            ],
                            [
                                'attribute'=>'IdState',
                                'filter'=> $searchModel->getStates(),
                                'content'=>function($model){
                                    return $model->IdState ? $model->state->Name:"";
                                },
                                'headerOptions' => ['style' => 'width:8%'],
                            ],
                            // 'created_at',
                            // 'updated_at',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => $template,
                                'buttons' => [
                                    'view'=> function($url, $model){
                                            return Html::a('<span class="fas fa-eye text-dark"></span>', $url, [
                                                'title' => Yii::t('app', 'Ver Usuario'), 
                                        ]);
                                    },
                                    'update'=> function($url, $model){
                                            return Html::a('<span class="fas fa-edit text-dark"></span>', $url, [
                                                'title' => Yii::t('app', 'Actualizar Usuario'), 
                                        ]);
                                    },
                                    'delete'=> function($url, $model){
                                            return Html::a('<span class="far fa-trash-alt text-dark"></span>', $url, [
                                                'title' => Yii::t('app', 'Eliminar Usuario'), 
                                        ]);
                                    },
                                ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
