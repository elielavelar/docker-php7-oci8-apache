<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $model backend\models\Servicecentreservices */
/* @var $modelDetail backend\models\Servicetask */
/* @var $searchModel \backend\models\ServicetaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$template = '{view}&nbsp;{update}&nbsp;{details}&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;{delete}';
$dtGrid = 'dt-grid';
?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="float-left">
                    <h4 class="card-title">Configuraci&oacute;n de Servicios</h4>
                </span>
            </div>
        </div>
        <div class="card-body">
            <p>
                <?= Html::button('<i class="fas fa-plus-circle"></i> Agregar Tarea', ['class' => 'btn btn-success','id' => 'btnAddTask']) ?>
            </p>
            <div class="card">
                <div class="row">
                    <div class="col-12">
                        <?=
                        GridView::widget([
                            'id' => $dtGrid,
                            'pjax' =>true,
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                #'Id',
                                'Name',
                                [
                                    'attribute'=>'Host',
                                    'headerOptions' => [
                                        'style' => 'width:10%',
                                    ],
                                ],
                                'Route',
                                [
                                    'attribute' => 'IdType',
                                    'content' => function($model) {
                                        return $model->IdType ? $model->type->Name : '';
                                    },
                                    'filter' => $modelDetail->getTypes(),
                                ],
                                [
                                    'attribute' => 'IdState',
                                    'content' => function($model) {
                                        return $model->IdState ? $model->state->Name : '';
                                    },
                                    'filter' => $modelDetail->getStates(),
                                    'headerOptions' => [
                                        'style' => 'width: 8%'
                                    ],
                                ],
                                //'Description:ntext',
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => $template,
                                    'buttons' => [
                                        'view' => function ($url, $model) {
                                            #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['servicecentreservice/view','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-eye"></span>', "javascript:viewDetail($model->Id);", [
                                                        'title' => Yii::t('app', 'Ver Servicio'),
                                            ]);
                                        },
                                        'update' => function ($url, $model) {
                                            #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['servicecentreservice/update','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-edit"></span>', "javascript:editDetail($model->Id);", [
                                                        'title' => Yii::t('app', 'Actualizar Servicio'),
                                            ]);
                                        },
                                        'details' => function ($url, $model) {
                                            $urlDetail = \Yii::$app->getUrlManager()->createUrl(['servicetask/update','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-th"></span>', $urlDetail, [
                                                        'title' => Yii::t('app', 'Detalles Tarea'),
                                            ]);
                                        },
                                        'delete' => function ($url, $model) {
                                            #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                                        'title' => Yii::t('app', 'Eliminar Servicio'),
                                            ]);
                                        },
                                    ],
                                ],
                            ],
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_modalFormDetail', ['model' => $model, 'modelDetail' => $modelDetail]); ?>
<?php
$script = <<< JS
   $(document).ready(function(){
        $("#btnAddTask").on('click', function(){
            $("#modal-detail").modal();
        });
   });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>