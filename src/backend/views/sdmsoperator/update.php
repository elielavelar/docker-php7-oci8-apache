<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\models\sdms\DatosOper */

$this->title = 'Actualizar Operador: ' . $model->COD_OPER;
$this->params['breadcrumbs'][] = ['label' => 'Operadores SDMS', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->COD_OPER, 'url' => ['view', 'id' => $model->COD_OPER]];
$this->params['breadcrumbs'][] = 'Actualizar';
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<?= $this->render('_form/_formModalViewPassword', ['model' =>  $model]);?>
<?= $this->render('_form/_formModalResetPassword', ['model' =>  $model]);?>
