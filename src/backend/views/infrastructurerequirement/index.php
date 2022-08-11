<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirement */
/* @var $searchModel backend\models\InfrastructurerequirementSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Requerimientos';
$this->params['breadcrumbs'][] = 'Infraestructura';
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$tableName = $model->tableName();

$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');
$filter = Yii::$app->customFunctions->userCan($controller.'Filter');

$filterState = $model->getStates();
$filterPriority = $model->getpriorityTypes();
$filterServicecentre = $model->getServiceCentres();

?>
<div class="infrastructurerequirement-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> Registrar', ['create'], ['class' => 'btn btn-success']) : ''?>
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
                            [
                                'attribute'=> 'Ticket',
                                'content' => function($model){
                                    return Html::a($model->Ticket, ['view','id' => $model->Id], []);
                                },
                                'headerOptions'=> [
                                    'style'=> 'width: 10%'
                                ],
                            ],
                            'Title',
                            ['attribute'=>'TicketDate',
                                'filterType'=> GridView::FILTER_DATE,
                                        'filterWidgetOptions'=> [
                                            'language'=>'es',
                                            'readonly'=>true,
                                            'pluginOptions'=> [
                                                'format'=>'dd-mm-yyyy',
                                                'autoclose'=>true,
                                                'todayHighlight' => true,
                                            ],
                                        ],
                                        'format' => 'html',
                                        'width'=>'10%',
                            ],
                            ['attribute'=>'RequirementDate',
                                'filterType'=> GridView::FILTER_DATE,
                                        'filterWidgetOptions'=> [
                                            'language'=>'es',
                                            'readonly'=>true,
                                            'pluginOptions'=> [
                                                'format'=>'dd-mm-yyyy',
                                                'autoclose'=>true,
                                                'todayHighlight' => true,
                                            ],
                                        ],
                                        'format' => 'html',
                                        'width'=>'10%',
                            ],
                            ['attribute'=>'SolutionDate',
                                'filterType'=> GridView::FILTER_DATE,
                                        'filterWidgetOptions'=> [
                                            'language'=>'es',
                                            'readonly'=>true,
                                            'pluginOptions'=> [
                                                'format'=>'dd-mm-yyyy',
                                                'autoclose'=>true,
                                                'todayHighlight' => true,
                                            ],
                                        ],
                                        'format' => 'html',
                                        'width'=>'10%',
                            ],
                            [
                                'attribute' => 'IdServiceCentre',
                                'filter' => $filterServicecentre,
                                'filterType' => GridView::FILTER_SELECT2,
                                'filterWidgetOptions' => [
                                    'theme' => Select2::THEME_BOOTSTRAP,
                                    'options' => [
                                        'placeholder' => '',
                                    ],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                    ],
                                ],
                                'content' => function($model) {
                                    return $model->IdServiceCentre ;
                                },
                                'width' => '18%',
                                'contentOptions' => [
                                    'style' => 'font-size:12px',
                                ]
                            ],
                            [
                                'attribute' => 'IdState',
                                'filter'=> $filterState,
                                'content'=> function($model){
                                    return $model->IdState;
                                },
                                'headerOptions'=> [
                                    'style'=> 'width: 8%'
                                ],
                            ],
                            [
                                'attribute' => 'IdPriorityType',
                                'filter'=> $filterPriority,
                                'content'=> function($model){
                                    return $model->IdPriorityType;
                                },
                                'headerOptions'=> [
                                    'style'=> 'width: 8%'
                                ],
                            ],
                            //'IdIncident',
                            //'IdState',
                            //'IdInfrastructureRequirementType',
                            //'IdReportUser',
                            //'IdUser',
                            //'AffectsFunctionality',
                            //'AffectsSecurity',
                            //'Quantity',
                            //'DamageDescription:ntext',
                            //'IdPriorityType',
                            //'SpecificLocation',
                            //'Description:ntext',
                            //'IdCreateUser',
                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
