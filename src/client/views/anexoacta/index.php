<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel client\models\AnexoactaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Anexoactas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="anexoacta-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Anexoacta', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'COD_CTRO_SERV',
            'COD_JEFE',
            'COD_DELEGADO',
            'FEC_FACTURACION',
            'FEC_ACTA',
            //'NUM_CORR_ACTA',
            //'PRIMERAVEZ',
            //'MODIFICACIONES',
            //'REPOSICIONES',
            //'RENOVACIONES',
            //'REIMPRESIONES',
            //'TAR_BASE_ANULADAS',
            //'TAR_DECAD_ANULADAS',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
