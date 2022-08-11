<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap4\Tabs;
use backend\models\Incidentrequest;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentrequest */
/* @var $searchModelRegistred backend\models\search\IncidentrequestSearch */
/* @var $dataProviderRegistred yii\data\ActiveDataProvider */
/* @var $searchModelProcess backend\models\search\IncidentrequestSearch */
/* @var $dataProviderProcess yii\data\ActiveDataProvider */
/* @var $searchModelApproved backend\models\search\IncidentrequestSearch */
/* @var $dataProviderApproved yii\data\ActiveDataProvider */
/* @var $searchModelRejected backend\models\search\IncidentrequestSearch */
/* @var $dataProviderRejected yii\data\ActiveDataProvider */
/* @var $searchModelClosed backend\models\search\IncidentrequestSearch */
/* @var $dataProviderClosed yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Service Requests');
$this->params['breadcrumbs'][] = $this->title;
$attributes = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'attribute' => 'Code',
        'content' => function( $model ){
            return Html::a($model->Code, ['update', 'id' => $model->Id]);
        },
        'headerOptions' => [
            'style' => 'width: 8%'
        ]
    ],
    [
            'attribute' => 'IncidentDate',
            'filterType' => GridView::FILTER_DATE,
            'filterWidgetOptions' => [
                'language'=>'es',
                'readonly'=>true,
                'pluginOptions'=> [
                    'format'=>'dd-mm-yyyy',
                    'autoclose'=>true,
                    'todayHighlight' => true,
                ],
            ],
            'format' => 'html',
            'headerOptions' => [
                'style' => 'width: 18%'
            ],
    ],
    [
        'attribute' => 'IdCategoryType',
        'filter' => $searchModelRegistred->getCategoryTypes(),
        'content' => function(Incidentrequest $model ){
            return $model->IdCategoryType ? $model->categoryType->Name : null;
        }
    ],
    [
        'attribute' => 'IdSubCategoryType',
        'filter' => false,
        'content' => function(Incidentrequest $model ){
            return $model->IdCategoryType ? $model->categoryType->Name : null;
        }
    ],
    'Description:ntext',
    ['class' => 'kartik\grid\ActionColumn'],
];
?>
<div class="incidentrequest-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['m+odel' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', '{icon} {action}',
                [
                    'icon' =>'<i class="fas fa-plus-circle"></i>',
                    'action' => Yii::t('app', 'Add')
                ]),
                ['create'],
                ['class' => 'btn btn-success']) ?>
    </p>

    <?= Tabs::widget([
        'items'=> [
            [
                'label'=> Yii::t('system', 'Registred'),
                'content'=>
                    Html::tag('div',
                        Html::tag('div',
                            GridView::widget([
                                'pjax' => true,
                                'dataProvider' => $dataProviderRegistred,
                                'filterModel' => $searchModelRegistred,
                                'columns' => $attributes,
                            ])
                        , [ 'class' => 'card-body'])
                    , [ 'class' => 'card']),
                'active' => true,
            ],
            [
                'label'=> Yii::t('system', 'In Process'),
                'content'=>
                    Html::tag('div',
                        Html::tag('div',
                            GridView::widget([
                                'pjax' => true,
                                'dataProvider' => $dataProviderProcess,
                                'filterModel' => $searchModelProcess,
                                'columns' => $attributes,
                            ])
                        , [ 'class' => 'card-body'])
                    , [ 'class' => 'card']),
                'active' => false,
            ],
            [
                'label'=> Yii::t('system', 'Approved'),
                'content'=>
                    Html::tag('div',
                        Html::tag('div',
                            GridView::widget([
                                'pjax' => true,
                                'dataProvider' => $dataProviderApproved,
                                'filterModel' => $searchModelApproved,
                                'columns' => $attributes,
                            ])
                        , [ 'class' => 'card-body'])
                    , [ 'class' => 'card']),
                'active' => false,
            ],
            [
                'label'=> Yii::t('system', 'Rejected'),
                'content'=>
                    Html::tag('div',
                        Html::tag('div',
                            GridView::widget([
                                'pjax' => true,
                                'dataProvider' => $dataProviderRejected,
                                'filterModel' => $searchModelRejected,
                                'columns' => $attributes,
                            ])
                        , [ 'class' => 'card-body'])
                    , [ 'class' => 'card']),
                'active' => false,
            ],
            [
                'label'=> Yii::t('system', 'Closed'),
                'content'=>
                    Html::tag('div',
                        Html::tag('div',
                            GridView::widget([
                                'pjax' => true,
                                'dataProvider' => $dataProviderClosed,
                                'filterModel' => $searchModelClosed,
                                'columns' => $attributes,
                            ])
                        , [ 'class' => 'card-body'])
                    , [ 'class' => 'card']),
                'active' => false,
            ],
        ]
    ]);
    ?>
</div>