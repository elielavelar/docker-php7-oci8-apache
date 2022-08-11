<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Resourcetype;

/* @var $this yii\web\View */
/* @var $model common\models\Resourcetype */
/* @var $searchModel common\models\search\ResourcetypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Resource type');
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = $view ? '{view}&nbsp;': '';
$template .= $update ? '&nbsp;{update}&nbsp;&nbsp;': '';
$template .= $delete ? '&nbsp;|&nbsp;&nbsp;{delete}': '';
?>
<div class="resourcetype-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fas fa-plus-circle"></i> '.Yii::t('app', '{action} {entity}', [
                'action' => Yii::t('app', 'Add'),
                'entity' => Yii::t('system', 'Resource type'),
            ]), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'kartik\grid\SerialColumn'],
                            [
                                    'attribute' => 'Id',
                                    'headerOptions' => [
                                        'width' => '10%'
                                    ]
                            ],
                            [
                                    'attribute' => 'IdType',
                                    'filter' => $model->getTypes(),
                                'content' => function( Resourcetype $model ){
                                    return $model->IdType ? $model->type->Name : '';
                                }
                            ],
                            [
                                'attribute' => 'Name',
                                'headerOptions' => [
                                    'width' => '35%'
                                ]
                            ],
                            'KeyWord',
                            'Code',
                            [
                                    'attribute' => 'IdState',
                                'filter' => $model->getStates(),
                                'content' => function( Resourcetype $model){
                                    return $model->IdState ? $model->state->Name : '';
                                }
                            ],
                            //'AgroupationType',
                            //'IdParent',
                            //'Description:ntext',

                            [
                                    'class' => 'kartik\grid\ActionColumn',
                                'template' => $template,
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
