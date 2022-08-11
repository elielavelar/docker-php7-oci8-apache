<?php
use kartik\grid\GridView;
use common\customassets\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidenttitle */
/* @var $searchModel backend\models\search\IncidentretitleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('system', 'Incident Titles');
$this->params['breadcrumbs'][] = $this->title;
$tableName = $model->tableName();
$dtGrid = $tableName.'-grid';

?>
<div class="incidenttitle-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', '{icon} {action}',[
            'icon' => Html::icon('fas fa-plus-circle'),
            'action' => Yii::t('app','Add')
        ]), ['create'], ['class' => 'btn btn-success']) ?>
        <span class="float-right">
            <?= $this->render('_form/_modalUpload', ['model' => $model])?>
        </span>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'id' => $dtGrid,
                        'pjax' => true,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'kartik\grid\SerialColumn'],
                                'Id',
                                'Title',
                                'IdCategoryType',
                                'Description:ntext',
                                'Enabled',

                            ['class' => 'kartik\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
