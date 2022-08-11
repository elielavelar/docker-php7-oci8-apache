<?php

use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Registredmodel */

$this->title = 'Agregar Modelo';
$this->params['breadcrumbs'][] = ['label' => 'Modelos de Sistema', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registredmodel-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
