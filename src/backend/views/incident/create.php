<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Incident */
/* @var $modelTitle backend\models\Incidenttitle */
/* @var $filterDepartments boolean */

$this->title = 'Registrar Ticket';
$this->params['breadcrumbs'][] = ['label' => 'Incidentes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model, 'filterDepartments'=> $filterDepartments,
        'modelTitle' => $modelTitle,
    ]) ?>

</div>
