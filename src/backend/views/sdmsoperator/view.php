<?php

use kartik\helpers\Html;
use kartik\detail\DetailView;
use backend\models\sdms\DatosOper;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model backend\models\sdms\DatosOper */

$this->title = $model->COD_OPER;
$this->params['breadcrumbs'][] = ['label' => 'Operadores SDMS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="datos-oper-view">

    <h1>Operador: <?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->COD_OPER], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->COD_OPER], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Está seguro que desea eliminar este Registro?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            'COD_OPER',
                            'NOM1_OPER',
                            'NOM2_OPER',
                            'NOM3_OPER',
                            'APDO1_OPER',
                            'APDO2_OPER',
                            [
                                'attribute'=>'COD_ROL',
                                'value'=> $model->COD_ROL ? $model->codRol->DESCRIPCION:'',
                            ],
                            [
                                'attribute'=>'COD_CARGO_OPER',
                                'value'=> $model->COD_CARGO_OPER ? $model->cargoOper->DESC_CARGO_OPER:'',
                            ],
                            [
                                'attribute'=>'STAT_OPER',
                                'value' => $model->STAT_OPER == DatosOper::STATUS_ACTIVE ? 'ACTIVO':'INACTIVO',
                            ],
                            [
                                'attribute'=>'COD_CTRO_SERV',
                                'value' => $model->COD_CTRO_SERV ? $model->ctroServ->DESC_CTRO_SERV:'',
                            ],
                            'COD_EMPLEADO',
                            'FECHA_CAMBIO',
                        ],
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <?=$this->render('_form/_formModalViewPassWord', ['model'=> $model])?>
                </div>
            </div>
        </div>
    </div>
    <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar', ['index'], ['class'=>'btn btn-danger'])?>
</div>
