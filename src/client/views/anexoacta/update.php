<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\prddui\Anexoacta */

$this->title = 'Update Anexoacta: ' . $model->COD_CTRO_SERV;
$this->params['breadcrumbs'][] = ['label' => 'Anexoactas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->COD_CTRO_SERV, 'url' => ['view', 'COD_CTRO_SERV' => $model->COD_CTRO_SERV, 'FEC_FACTURACION' => $model->FEC_FACTURACION]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="anexoacta-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
