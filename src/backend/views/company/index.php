<?php
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Company */
/* @var $searchModel common\models\search\CompanySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Organización';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => \kartik\grid\SerialColumn::class],
                            'Id',
                            'Name',
                            'TaxRegistrationNumber',
                            'TaxIdentificationNumber',
                            [
                                'attribute' => 'IdSizeType',
                                'content' => function($model){
                                    return $model->IdSizeType ? $model->sizeType->Name : '';
                                }
                            ],
                            //'TradeName',
                            //'BusinessSector',
                            [
                                'attribute' => 'Enabled',
                                'content' => function($model){
                                    return $model->Enabled == $model::ENABLED ? 'Sí':'No';
                                },
                            ],
                            //'Description:ntext',

                            ['class' => kartik\grid\ActionColumn::class],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
