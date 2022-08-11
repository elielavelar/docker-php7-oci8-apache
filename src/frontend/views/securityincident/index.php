<?php

use kartik\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use backend\models\Securityincident;
use common\models\State;
use common\models\Type;
use backend\models\Incidentcategory;
use common\models\Servicecentres;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SecurityincidentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Incidentes de Seguridad';
$this->params['breadcrumbs'][] = $this->title;

$tableName = StringHelper::basename(Securityincident::tableName());

$template = "";
$template .= $view ? '{view} ' : '';
$template .= $update ? '{update} ' : '';

$filterServicecentre = ArrayHelper::map(Servicecentres::find()
                        ->joinWith('state b')
                        ->where(['b.KeyWord' => StringHelper::basename(Servicecentres::class)
                            , 'b.Code' => Servicecentres::STATE_ACTIVE,
                        ])->all()
                , 'Id', 'Name');
$filterState = ArrayHelper::map(State::findAll(['KeyWord' => StringHelper::basename(Securityincident::class)]), 'Id', 'Name');
$filterRisk = ArrayHelper::map(Type::find()
                        ->joinWith('state b')
                        ->where([
                            'b.KeyWord' => StringHelper::basename(Type::class),
                            'b.Code' => Type::STATUS_ACTIVE,
                            'type.KeyWord' => StringHelper::basename(Securityincident::class) . "Level"
                        ])
                        ->orderBy(['type.Value' => SORT_ASC])
                        ->all()
                , 'Id', 'Name');
?>
<div class="securityincident-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?= Yii::$app->customFunctions->userCan($tableName . 'Create') ? Html::a('<i class="fas fa-plus-circle"></i> Crear Incidencia', ['create'], ['class' => 'btn btn-success']) : "" ?>
    </p>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <?php Pjax::begin(); ?>
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            #['class' => 'yii\grid\SerialColumn'],
                            #'Id',
                            [
                                'attribute' => 'Ticket',
                                'headerOptions' => [
                                    'style' => 'width: 10%'
                                ],
                                'content' => function($model){
                                    return Html::a($model->Ticket, ['view','id' => $model->Id], []);
                                },
                            ],
                            [
                                'attribute' => 'TicketDate',
                                'headerOptions' => [
                                    'style' => 'width: 8%'
                                ],
                            ],
                            [
                                'attribute' => 'IncidentDate',
                                'headerOptions' => [
                                    'style' => 'width: 8%'
                                ],
                            ],
                            [
                                'attribute' => 'SolutionDate',
                                'headerOptions' => [
                                    'style' => 'width: 8%'
                                ],
                            ],
                            [
                                'attribute' => 'IdServiceCentre',
                                'filter' => $filterServicecentre,
                                'filterType' => GridView::FILTER_SELECT2,
                                'filterWidgetOptions' => [
                                    'disabled' => !$filterDepartment,
                                    'size' => 'md',
                                    'theme' => \kartik\select2\Select2::THEME_BOOTSTRAP,
                                    'options' => [
                                        'placeholder' => '',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ],
                                'content' => function($model) {
                                    return $model->IdServiceCentre ? $model->serviceCentre->Name : "";
                                },
                                'width' => '18%',
                                'contentOptions' => [
                                    'style' => 'font-size:12px',
                                ]
                            ],
                            //'InterruptDate',
                            //'SolutionDate',
                            'Title',
                            [
                                'attribute' => 'IdState',
                                'filter' => $filterState,
                                'content' => function($model) {
                                    return $model->IdState ? $model->state->Name : '';
                                },
                                'headerOptions' => [
                                    'style' => 'width: 10%'
                                ],
                            ],
                            [
                                'attribute' => 'IdLevelType',
                                'filter' => $filterRisk,
                                'content' => function($model) {
                                    return $model->IdLevelType ? $model->levelType->Name : '';
                                },
                                'headerOptions' => [
                                    'style' => 'width: 10%'
                                ],
                            ],
                            //'IdIncident',
                            //'IdReportUser',
                            //'IdType',
                            //'IdState',
                            //'IdLevelType',
                            //'IdPriorityType',
                            //'IdInterruptType',
                            //'IdUser',
                            //'IdCreateUser',
                            //'IdGravityType',
                            //'IdCategoryType',
                            //'Description:ntext',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => $template
                            ],
                        ],
                    ]);
                    ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>
