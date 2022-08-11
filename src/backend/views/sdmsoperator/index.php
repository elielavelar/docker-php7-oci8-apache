<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use backend\models\sdms\DatosOper;
use backend\models\sdms\CatCargoOper;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\sdms\DatosoperSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$model = new DatosOper();
$ctroServFilter = $model->getCtroServs();
$cargoOperFilter = $model->getCargosOper();
$codRolFilter = $model->getCodRols();
#print_r($cargoOperFilter); die();

$this->title = 'Operadores SDMS';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="datos-oper-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fas fa-plus-circle"></i> Agregar Operador', ['create'], ['class' => 'btn btn-success']) ?>
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

                            [
                                'attribute'=>'COD_OPER',
                                'headerOptions' => [
                                    'style'=> 'width:8%'
                                ]
                            ],
                            [
                                'attribute' => 'nameOper',
                            ],
                            #'NOM1_OPER',
                            #'NOM2_OPER',
                            //'NOM3_OPER',
                            #'APDO1_OPER',
                            #'APDO2_OPER',
                            [
                                'attribute'=>'COD_ROL',
                                'filter' => $codRolFilter,
                                'content' => function($model){
                                    return $model->COD_ROL ? $model->codRol->DESCRIPCION:'';
                                },
                                'headerOptions' => [
                                    'style'=> 'width: 15%'
                                ],
                            ],
                            [
                                'attribute'=>'COD_CARGO_OPER',
                                'filter' => $cargoOperFilter,
                                'content' => function($model){
                                    return $model->COD_CARGO_OPER ? $model->cargoOper->DESC_CARGO_OPER:'';
                                },
                                'headerOptions' => [
                                    'style'=> 'width: 15%'
                                ],
                            ],
                            [
                                'attribute'=>'COD_CTRO_SERV',
                                'filter' => $ctroServFilter,
                                'content' => function($model){
                                    return $model->COD_CTRO_SERV ? $model->ctroServ->DESC_CTRO_SERV:'';
                                }
                            ],
                            [
                                'attribute'=>'STAT_OPER',
                                'filter' => ['A' => 'ACTIVO', 'I' => 'INACTIVO'],
                                'content' => function($model){
                                    return $model->STAT_OPER ? $model->NOM_STAT: '';
                                },
                                'headerOptions' => [
                                    'style'=> 'width:10%'
                                ]
                            ],
                            //'COD_EMPLEADO',
                            //'FECHA_CAMBIO',

                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
