<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use common\models\Zone;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ZoneSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zonas';
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$tableName = $model->tableName();
$formName = $tableName."-form";
$url = \Yii::$app->getUrlManager()->createUrl('zone');

$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = '';
$template .= $view ? '{view} &nbsp;' : '';
$template .= $update ? '{update} &nbsp;' : '';
$template .= $delete ? '|&nbsp;&nbsp;&nbsp;{delete}' : '';

?>
<div class="zones-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> Agregar Zona', ['create'],['id'=>'btn-addZone','class' => 'btn btn-success'])  : '';?>
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

                            'Name',
                            'Code',
                            [
                                'attribute'=>'IdState',
                                'filter'=> ArrayHelper::map(State::find()->where(['KeyWord'=>  StringHelper::basename(Zone::className())])->select(['Id','Name'])->all(), 'Id', 'Name'),
                                'content'=>function($model){
                                    return $model->IdState ? $model->state->Name:"";
                                },
                            ],
                            'Description:ntext',

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => $template,
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>