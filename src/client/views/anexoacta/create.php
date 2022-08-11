<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\prddui\Anexoacta */

$this->title = 'Create Anexoacta';
$this->params['breadcrumbs'][] = ['label' => 'Anexoactas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="anexoacta-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
