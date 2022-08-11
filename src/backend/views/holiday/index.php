<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\Type;
use common\models\State;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\HolidaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Días Feriados';
$this->params['breadcrumbs'][] = $this->title;

$create = Yii::$app->user->can('holidayCreate');

$filterState= ArrayHelper::map(State::findAll(['KeyWord'=>'Holidays']), 'Id', 'Name');
$filterType = ArrayHelper::map(Type::findAll(['KeyWord'=>'Holidays']), 'Id', 'Name');
$filterTypeFreq = ArrayHelper::map(Type::findAll(['KeyWord'=>'HolidaysFrequency']), 'Id', 'Name');

?>
<div class="holidays-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('Crear', ['create'], ['class' => 'btn btn-success']):""; ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id',
            'Name',
            'Description:ntext',
            [
                'attribute'=> 'IdType',
                'filter'=> Html::activeDropDownList($searchModel, 'IdType', $filterType, ['class'=>'form-control','prompt'=>'--']),
                'content'=>function($data){
                    return $data->IdType != 0 ? $data->type->Name:NULL;
                },
            ],
            [
                'attribute'=> 'IdFrequencyType',
                'filter'=> Html::activeDropDownList($searchModel, 'IdFrequencyType', $filterTypeFreq, ['class'=>'form-control','prompt'=>'--']),
                'content'=>function($data){
                    return $data->IdFrequencyType != 0 ? $data->frequencyType->Name:NULL;
                },
            ],
            [
                'attribute'=> 'IdState',
                'filter'=> Html::activeDropDownList($searchModel, 'IdState', $filterState, ['class'=>'form-control','prompt'=>'--']),
                'content'=>function($data){
                    return $data->IdState != 0 ? $data->state->Name:NULL;
                },
            ],
            // 'DateStart',
            // 'DateEnd',
            // 'IdFrequencyType',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
