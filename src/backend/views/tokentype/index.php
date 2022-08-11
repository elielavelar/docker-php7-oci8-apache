<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use backend\models\TokenType;
use common\models\Type;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\TokentypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tipos de Token';
$this->params['breadcrumbs'][] = $this->title;
$filterState = ArrayHelper::map(State::findAll(['KeyWord' => StringHelper::basename(Type::class)]),'Id','Name');
?>
<div class="token-type-index">

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
                            #'Id',
                            'Name',
                            'Code',
                            'Value',
                            [
                                'attribute' => 'IdState',
                                'filter' => $filterState,
                                'content' => function($model) {
                                    return $model->IdState ? $model->state->Name : '';
                                },
                            ],
                            //'Description',
                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
