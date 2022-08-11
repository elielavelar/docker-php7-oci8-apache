<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\grid\ExpandRowColumn;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $modelDetail common\models\Personaldocument */
/* @var $searchModel common\models\search\PersonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Personas';
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view= Yii::$app->customFunctions->userCan($controller.'View');

$template = '';
$template .= $view ? '&nbsp;&nbsp;{view}': '';
$template .= $update ? '&nbsp;&nbsp;{update}': '';
$template .= $delete ? '&nbsp;&nbsp;&nbsp;|&nbsp;{delete}': '';

?>
<div class="person-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?=$this->render('_search', ['model' => $searchModel, 'modelDetail' => $modelDetail]); ?>

    <p>
        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar Persona', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        #'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'Code',
                                'width' => '5%'
                            ],
                            [
                                'attribute' => 'completeName',
                                'width' => '25%'
                            ],
                            //'SecondLastName',
                            //'MarriedName',
                            [
                                'attribute' => 'profiles',
                                'content' => function($model){
                                    return $model->getProfiles();
                                }
                            ],
                            [
                                'attribute' => 'documents',
                                'content' => function($model){
                                    return $model->getDocuments();
                                }
                            ],
//                            [
//                                'class' => ExpandRowColumn::class,
//                                'width' => '50px',
//                                'value' => function ($model, $key, $index, $column) {
//                                    return GridView::ROW_COLLAPSED;
//                                },
//                                // uncomment below and comment detail if you need to render via ajax
//                                 'detailUrl' => Url::to(['personaldocument/detail']),
//                                'detail' => function ($model, $key, $index, $column) {
//                                    return Yii::$app->controller->renderPartial('_form/_rowdetail', ['model' => $model]);
//                                },
//                                'headerOptions' => ['class' => 'kartik-sheet-style'] ,
//                                'expandOneOnly' => true
//                            ],
                            [
                                'attribute' => 'IdGenderType',
                                'content' => function($model){
                                    return $model->IdGenderType ? $model->genderType->Name : '';
                                }
                            ],
                            [
                                'attribute' => 'IdState',
                                'content' => function($model){
                                    return $model->IdState ? $model->state->Name : '';
                                }
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
