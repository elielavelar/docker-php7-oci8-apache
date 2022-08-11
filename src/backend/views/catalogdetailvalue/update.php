<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetailvalues */

$this->title = 'Update Catalogdetailvalues: ' . $model->Id;
$this->params['breadcrumbs'][] = ['label' => 'Catalogdetailvalues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="catalogdetailvalues-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
