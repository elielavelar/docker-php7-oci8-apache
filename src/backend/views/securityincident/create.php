<?php

use yii\helpers\Html;
use backend\models\Securityincident;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */

$this->title = 'Registrar Incidencia de Seguridad';
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$admin = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Admin');
$create = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Create');
$update = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Update');
$close = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Close');

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
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model, 'admin'=> $admin, 'create'=> $create, 'update' => $update, 'close'=> $close, 'save' => $save,
    ]) ?>

</div>
