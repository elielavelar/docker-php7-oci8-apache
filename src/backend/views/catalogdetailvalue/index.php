<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CatalogdetailvaluesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Catalogdetailvalues';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogdetailvalues-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Catalogdetailvalues', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id',
            'IdCatalogDetail',
            'IdDataType',
            'IdValueType',
            'Value',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
