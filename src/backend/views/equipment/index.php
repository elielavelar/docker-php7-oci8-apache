<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Equipment;

/* @var $this yii\web\View */
/* @var $model common\models\Equipment */
/* @var $searchModel common\models\search\EquipmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Equipments');
$this->params['breadcrumbs'][] = $this->title;
$controller = Yii::$app->controller->id;
$filter = Yii::$app->customFunctions->userCan( $controller.'Filter');
?>
<div class="equipment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= $filter ? $this->render('_search', ['model' => $searchModel]) : ''; ?>

    <p>
        <?= Html::a('<i class="fas fa-plus-circle"></i> '.
            Yii::t('app', '{action} {entity}', [
                    'action' => Yii::t('app', 'Add'),
                    'entity' => Yii::t('system', 'Equipment'),
            ])
            , ['create'], ['class' => 'btn btn-success']) ?>
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

                            'Id',
                            'Name',
                            'Code',
                            [
                                'attribute' => 'IdResourceType',
                                'filter' => $model->getResourceTypes(),
                                'content' => function( Equipment $model ){
                                    return $model->IdType ? $model->type->Name : '';
                                }
                            ],
                            [
                                'attribute' => 'IdServiceCentre',
                                'filter' => $model->getServiceCentres(),
                                'content' => function( Equipment $model ){
                                    return $model->IdServiceCentre ? $model->serviceCentre->Name : '';
                                }
                            ],
                            //'IdState',
                            //'CreationDate',
                            //'IdUserCreation',
                            //'LastUpdateDate',
                            //'IdUserLastUpdate',
                            //'IdParent',
                            //'Description:ntext',
                            //'TokenId',

                            ['class' => 'kartik\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
