<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Field */

$this->title = 'Agregar Campo';
$this->params['breadcrumbs'][] = ['label' => 'Campos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fields-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model, 'modelSource' => $modelSource,
        ]) ?>
    </div>
</div>
