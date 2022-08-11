<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\State;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CitizenSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Ciudadanos';
$this->params['breadcrumbs'][] = $this->title;
$filterState= ArrayHelper::map(State::findAll(['KeyWord'=>'Citizen']), 'Id', 'Name');
?>
<div class="citizen-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Registrar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            #'Id',
            'Name',
            'LastName',
            [
                'attribute'=>'Email',
                'content'=>function($model){
                    return $model->Email ? Html::mailto($model->Email):'-';
                }
            ],
            [
                'attribute'=>'IdState',
                'filter'=> Html::activeDropDownList($searchModel, 'IdState', $filterState, ['class'=>'form-control','prompt'=>'--']),
                'content'=>function($data){
                    return $data->IdState != 0 ? $data->state->Name:NULL;
                },
                'enableSorting'=>TRUE  
            ],
            [
                'attribute'=>'Telephone',
                'content'=>function($model){
                    return $model->Telephone ? $model->Telephone:"-";
                }
            ],
            //'PasswordHash',
            // 'PasswordResetToken',
            // 'AuthKey',
            // 'CreateDate',
            // 'UpdateDate',
            // 'IdState',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return $model->view ? "<a href='$url';'><span class='glyphicon glyphicon-eye-open'></span></a>":"";
                    },
                    'update'=>function ($url, $model) {
                        return $model->update ? "<a href='$url';'><span class='glyphicon glyphicon-pencil'></span></a>":"";
                    },
                    'delete'=>function ($url, $model) {
                        return $model->delete ? "<a href='$url';'><span class='glyphicon glyphicon-trash'></span></a>":"";
                    },
                ],
            ],
        ],
    ]); ?>
</div>
