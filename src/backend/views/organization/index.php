<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Organization */
/* @var $searchModel common\models\search\OrganizationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Organization');
$this->params['breadcrumbs'][] = $this->title;

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller . 'Create');
$update = Yii::$app->customFunctions->userCan($controller . 'Update');
$delete = Yii::$app->customFunctions->userCan($controller . 'Delete');
$view = Yii::$app->customFunctions->userCan($controller . 'View');

?>
<div class="organization-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= $create ? Html::a('<i class="fas fa-plus-circle"></i> ' .
            Yii::t('app', '{action} {entity}', [
                'action' => Yii::t('app', 'Add'),
                'entity' => Yii::t('app', 'Organization')
            ]), ['create'], ['class' => 'btn btn-success']) : null ?>
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
                            'Enabled',
                            'DefaultEntity',

                            ['class' => 'kartik\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
