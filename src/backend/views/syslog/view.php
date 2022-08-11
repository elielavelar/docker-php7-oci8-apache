<?php

use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Syslog */

$this->title = $model->Title;
$this->params['breadcrumbs'][] = ['label' => 'Bitácora del Sistema', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$tableName = $model->tableName();
$delete = Yii::$app->customFunctions->userCan($tableName.'Delete');

?>
<div class="syslog-view">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', ['model' => $model]),
                        'active' => true
                    ],
                    [
                        'label' => 'Detalles',
                        'content' => $this->render('_form/_detail',['model'=>$model, 'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,'modelDetail'=>$modelDetail,]),
                    ],
                ]]);
         ?>
    </div>
    <div class="row">
        <div class="col-12">
            <span class="float-left">
                <?= Html::a('<i class="fas fa-arrow-circle-left"></i> Cancelar',['index'],['class'=> 'btn btn-danger'])?>
            </span>
            <span class="float-right">
                <?= $delete ?  Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea Eliminar este Registro?',
                        'method' => 'post',
                    ],
                ]):"" ?>
            </span>
        </div>
    </div>

</div>
