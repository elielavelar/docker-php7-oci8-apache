<?php
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Resource */
/* @var $searchModel common\models\search\ResourceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Resources');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('system', '<i class=\"fas fa-plus-circle\"></i> Agregar Resource'), ['create'], ['class' => 'btn btn-success']) ?>
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
            'IdType',
            'Name',
            'Code',
            'IdResourceType',
            //'IdServiceCentre',
            //'IdState',
            //'CreationDate',
            //'IdUserCreation',
            //'LastUpdateDate',
            //'IdUserLastUpdate',
            //'IdParent',
            //'Description:ntext',

                            ['class' => 'kartik\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
