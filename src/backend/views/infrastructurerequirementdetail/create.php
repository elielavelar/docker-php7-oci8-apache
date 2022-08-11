<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirementdetails */

$this->title = 'Agregar Infrastructurerequirementdetails';
$this->params['breadcrumbs'][] = ['label' => 'Infrastructurerequirementdetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="infrastructurerequirementdetails-create">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>
