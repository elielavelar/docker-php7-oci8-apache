<?php

use yii\helpers\Html;
use backend\models\Infrastructurerequirement;

/* @var $this yii\web\View */
/* @var $model backend\models\Infrastructurerequirement */

$this->title = 'Agregar Requerimiento';
$this->params['breadcrumbs'][] = 'Infraestructura';
$this->params['breadcrumbs'][] = ['label' => 'Requerimientos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$permission = (!isset($permission) ? [] : $permission);
$admin = isset($permission['admin']) ? $permission['admin']: false;
$create = isset($permission['create']) ? $permission['create']: false;
$update = isset($permission['update']) ? $permission['update']: false;
$delete = isset($permission['delete']) ? $permission['delete']: false;
$view = isset($permission['view']) ? $permission['view']: false;
$filterDepartment = isset($permission['filterDepartment']) ? $permission['filterDepartment']: false;

$save = FALSE;
switch ($model->state->Code){
    case $model::STATE_PENDENT:
        $save = $model->isNewRecord ? $create:$update;
        break;
    case $model::STATE_INPROCESS:
        $save = $update || $close;
        break;
    default :
        break;
}
$permission['save'] = $save;
?>
<div class="infrastructurerequirement-create">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model, 'permission'=> $permission,
        ]) ?>
    </div>
</div>
