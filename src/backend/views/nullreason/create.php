<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Nullreasons */

$this->title = 'Create Nullreasons';
$this->params['breadcrumbs'][] = ['label' => 'Nullreasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nullreasons-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
