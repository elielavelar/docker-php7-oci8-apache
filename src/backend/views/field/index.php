<?php

use yii\bootstrap4\Html;
use kartik\grid\GridView;
use common\models\Type;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model common\models\Field */
/* @var $searchModel common\models\search\FieldSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Campos';
$this->params['breadcrumbs'][] = 'Catálogos';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="fields-index">

    <h1><?= Html::encode($this->title) ?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?=
                    GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],
                            [
                                'attribute' => 'Id',
                                'width' => '5%',
                            ],
                            'Name',
                            'KeyWord',
                            'Code',
                            [
                                'attribute' => 'UseMask',
                                'filter' => [$model::USE_MASK_DISABLED => 'No', $model::USE_MASK_ENABLED => 'Sí'],
                                'content' => function($model){
                                    return $model->UseMask == $model::USE_MASK_ENABLED ? 'Sí' : 'No';
                                },
                            ],
                            [
                                'attribute' => 'HasCatalog',
                                'filter' => [$model::HAS_CATALOG_FALSE => 'No', $model::HAS_CATALOG_TRUE => 'Sí'],
                                'content' => function($model){
                                    return $model->HasCatalog == $model::HAS_CATALOG_TRUE ? 'Sí' : 'No';
                                },
                            ],
                            [
                                'attribute' => 'IdType',
                                'filter' => $model->getTypes(),
                                'content' => function($model){
                                    return $model->IdType ? $model->type->Name : '';
                                },
                            ],
                            [
                                'attribute' => 'IdState',
                                'filter' => $model->getStates(),
                                'content' => function($model){
                                    return $model->IdState ? $model->state->Name : '';
                                },
                            ],
                            //'HasCatalog',
                            //'Description:ntext',
                            //'Value',
                            //'MultipleValue',
                            ['class' => kartik\grid\ActionColumn::class],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
