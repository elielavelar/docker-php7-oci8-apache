<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Catalogversions */

$this->title = 'Create Catalogversions';
$this->params['breadcrumbs'][] = ['label' => 'Catalogversions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogversions-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
