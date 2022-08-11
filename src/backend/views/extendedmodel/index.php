<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Extendedmodel */
/* @var $searchModel common\models\search\ExtendedmodelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Modelos Extendidos';
$this->params['breadcrumbs'][] = 'Configuraciones';
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
<div class="extendedmodels-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
    <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'IdRegistredModel',
                                'content' => function($model){
                                    return $model->IdRegistredModel ? $model->registredModel->Name : '';
                                },
                            ],
                            'keyword',
                            [
                                'attribute' => 'IdNameSpace',
                                'content' => function($model){
                                    return $model->IdNameSpace ? $model->nameSpace->Name : '';
                                },
                                'filter' => $model->getNameSpaces(),
                            ],
                            [
                                'attribute' => 'IdState',
                                'content' => function($model){
                                    return $model->IdState ? $model->state->Name : '';
                                },
                                'filter' => $model->getStates(),
                            ],
                            //'Description:ntext',
                            [
                                'class' => kartik\grid\ActionColumn::class,
                                'template' => $template,
                            ],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
