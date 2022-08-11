<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Activetype */

$this->title = 'Create Activetype';
$this->params['breadcrumbs'][] = ['label' => 'Activetypes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="activetype-create">

    <h4 class="card-title"><?= Html::encode($this->title) ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
