<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\State;
use backend\models\Nullreasons;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\NullreasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Razones de Anulación';
$this->params['breadcrumbs'][] = $this->title;

$tableName = Nullreasons::tableName();

$template = "";
$template .= Yii::$app->customFunctions->userCan($tableName."Update") ? "":"";

?>
<div class="nullreasons-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->customFunctions->userCan($tableName.'Create') ? Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']):"" ;?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'Id',
            'Name',
            'Code',
            'IdState',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
