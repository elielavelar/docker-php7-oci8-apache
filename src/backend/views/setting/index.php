<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use backend\models\Setting;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SettingSearch */
/* @var $model Setting */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Parámetros del Sistema';
$this->params['breadcrumbs'][] = 'Configuración';
$this->params['breadcrumbs'][] = $this->title;

$tableName = $model->tableName();
$frmName = 'form-'.$tableName;
$url =  \Yii::$app->getUrlManager()->createUrl('setting');

$controller = Yii::$app->controller->id;
$template = '';
$template .= Yii::$app->customFunctions->userCan($controller.'View') ? '&nbsp;&nbsp;{view}':'';
$template .= Yii::$app->customFunctions->userCan($controller.'Update') ? '&nbsp;&nbsp;{update}':'';
$template .= Yii::$app->customFunctions->userCan($controller.'Delete') ? '&nbsp;&nbsp;|&nbsp;&nbsp;{delete}':'';

?>
<div class="versions-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Yii::$app->customFunctions->userCan('settingCreate') ? Html::a('<i class="fas fa-plus-circle"></i> Crear Parámetro', ['create'], ['class' => 'btn btn-success']):"" ?>
    </p>

    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'pjax' => true,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            'Name',
                            'KeyWord',
                            'Code',
                            [
                                'attribute' => 'IdType',
                                'filter' => $model->getTypes(),
                                'content' => function($model){
                                    return $model->IdType ? $model->type->Name:'';
                                }
                            ],
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
