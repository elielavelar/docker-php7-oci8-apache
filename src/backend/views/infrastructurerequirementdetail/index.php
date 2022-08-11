<?php
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirementdetails */
/* @var $searchModel backend\models\InfrastructurerequirementdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Infrastructurerequirementdetails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructurerequirementdetails-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class=\"fas fa-plus-circle\"></i> Agregar Infrastructurerequirementdetails', ['create'], ['class' => 'btn btn-success']) ?>
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

                            'Id',
            'IdInfrastructureRequirement',
            'Title',
            'Description:ntext',
            'DetailDate',
            //'RecordDate',
            //'SolutionDate',
            //'IdUser',
            //'IdActivityType',
            //'IdRequirementState',
            //'IdAssignedUser',
            //'Commentaries:ntext',
            //'IdCatalogDetailValue',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
