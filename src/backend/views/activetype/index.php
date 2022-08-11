<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ActivetypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Activetypes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activetype-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Activetype', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id',
            'Name',
            'IdState',
            'IdCategoryType',
            'Description',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
