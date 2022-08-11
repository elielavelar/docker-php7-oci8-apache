<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\prddui\Anexoacta */

$this->title = $model->COD_CTRO_SERV;
$this->params['breadcrumbs'][] = ['label' => 'Anexoactas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="anexoacta-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'COD_CTRO_SERV' => $model->COD_CTRO_SERV, 'FEC_FACTURACION' => $model->FEC_FACTURACION], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'COD_CTRO_SERV' => $model->COD_CTRO_SERV, 'FEC_FACTURACION' => $model->FEC_FACTURACION], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'COD_CTRO_SERV',
            'COD_JEFE',
            'COD_DELEGADO',
            'FEC_FACTURACION',
            'FEC_ACTA',
            'NUM_CORR_ACTA',
            'PRIMERAVEZ',
            'MODIFICACIONES',
            'REPOSICIONES',
            'RENOVACIONES',
            'REIMPRESIONES',
            'TAR_BASE_ANULADAS',
            'TAR_DECAD_ANULADAS',
        ],
    ]) ?>

</div>
