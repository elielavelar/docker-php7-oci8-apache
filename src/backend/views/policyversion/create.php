<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Policyversions */

$this->title = 'Agregar '.($model->policy->IdType ? $model->policy->type->Name:'');
$this->params['breadcrumbs'][] = ['label' => 'Políticas y Procedimientos', 'url' => ['policy/index']];
$this->params['breadcrumbs'][] = ['label' => $model->policy->Code, 'url' => ['policy/view', 'id' => $model->IdPolicy]];
$this->params['breadcrumbs'][] = 'Agregar Versión';
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', ['model' => $model]); ?>
</div>
