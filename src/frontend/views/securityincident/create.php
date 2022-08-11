<?php

use yii\helpers\Html;
use backend\models\Securityincident;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */

$this->title = 'Registrar Incidencia de Seguridad';
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$save = FALSE;
switch ($model->state->Code){
    case Securityincident::STATE_REGISTRED:
        $save = $model->isNewRecord ? $create:$update;
        break;
    case Securityincident::STATE_INPROCESS:
        $save = $update || $close;
        break;
    default :
        break;
}

?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model, 'admin'=> $admin, 'create'=> $create, 'update' => $update, 'close'=> $close, 'save' => $save,
    ]) ?>

</div>
