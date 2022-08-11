<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use backend\models\Securityincident;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */

$this->title = 'Actualizar Incidencia de Seguridad: ' . $model->Ticket;
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Ticket, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$admin = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Admin');
$create = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Create');
$update = ( $admin || ($model->IdUser == Yii::$app->user->getIdentity()->getId() && Yii::$app->customFunctions->userCan(Securityincident::tableName().'Update')));
$close = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Close');
$view = Yii::$app->customFunctions->userCan(Securityincident::tableName().'View');
$delete = Yii::$app->customFunctions->userCan(Securityincident::tableName().'Delete');

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
<div class="panel panel-primary">
    <div class="panel-heading">
        <h4 class="panel-title"><?= Html::encode($this->title) ?></h4>
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
<?php
$disabled = $admin ? '': '$("input, select, textarea").attr("disabled", true);';
$js = <<< JS
   $(document).ready(function(){
        $disabled
   });
JS;
$this->registerJs($js, View::POS_READY);
?>