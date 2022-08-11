<?php

use yii\bootstrap4\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Resource */

$this->title = Yii::t('system', 'Agregar Resource');
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Resources'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resource-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
