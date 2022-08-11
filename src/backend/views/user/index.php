<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap4\Modal;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;

/* @var $this yii\web\View */
/* @var $model \common\models\User */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
$controller = Yii::$app->controller->id;

$url = Yii::$app->getUrlManager()->createUrl($controller);

$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = '';
$template .= $view ? '{view}&nbsp;&nbsp;':'';
$template .= $update ? '{update}&nbsp;&nbsp;':'';
$template .= $delete ? '&nbsp;|&nbsp;&nbsp;&nbsp;{delete}':'';

?>
<div class="user-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= $this->render('_search', ['model' => $searchModel]); ?>

    <?php if($create):?>
    <p>
        <?=Html::a('<i class="fas fa-plus-circle"></i> Crear Usuario', ['create'], ['class' => 'btn btn-success']); ?>
        <span class="float-right">
            <?= $this->render('_form/_modalBatchUpload', ['model' => $model]) ?>
        </span>
    </p>
    <?php endif; ?>
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
