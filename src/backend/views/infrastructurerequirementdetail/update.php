<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirementdetails */

$this->title = 'Actualizar Infrastructurerequirementdetails: ' . $model->Title;
$this->params['breadcrumbs'][] = ['label' => 'Infrastructurerequirementdetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Title, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="infrastructurerequirementdetails-update">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
