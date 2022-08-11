<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use backend\models\Securityincident;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */

$this->title = 'Actualizar Incidencia de Seguridad: ' . $model->Ticket;
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Ticket, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$controller = Yii::$app->controller->id;
$admin = Yii::$app->customFunctions->userCan($controller.'Admin');
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$close = Yii::$app->customFunctions->userCan($controller.'Close');
$view = Yii::$app->customFunctions->userCan($controller.'View');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');

$save = FALSE;
switch ($model->state->Code){
    case Securityincident::STATE_REGISTRED:
        $save = $model->isNewRecord ? $create:$update;
        $delete = $delete && TRUE;
        break;
    case Securityincident::STATE_INPROCESS:
        $save = $update || $close;
        $delete = $delete && TRUE;
        break;
    default :
        $delete = $delete && FALSE;
        break;
}

$permissions = [
    'create' => $create, 'update' => $update, 'view'=> $view,
    'admin'=> $admin,'close'=> $close, 'save' => $save,
    'delete'=> $delete
];

?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', array_merge($permissions,['model' => $model])),
                    'active' => true
                ],
                [
                    'label' => 'Detalles',
                    'content' => $this->render('_form/_detail',array_merge($permissions,['model'=>$model, 
                        'searchModel'=>$searchModel, 'dataProvider'=>$dataProvider,
                        'modelDetail'=>$modelDetail])),
                ],
                [
                    'label' => 'Adjuntos',
                    'content' => $this->render('_form/_attachments', array_merge($permissions,['model'=>$model,
                        'searchModel'=>$searchAttachmentModel, 
                        'dataProvider'=>$attachmentDataProvider,'modelDetail'=>$attachmentModel,])
                    ),
                ],
            ]]);
     ?>
</div>