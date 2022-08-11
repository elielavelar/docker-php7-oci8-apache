<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use backend\models\Securityincident;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */

$this->title = 'Actualizar Incidencia de Seguridad: ' . $model->Ticket;
$this->params['breadcrumbs'][] = ['label' => 'Incidencias de Seguridad', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->Ticket;

$permissions = [
    'create' => false, 'update' => false, 'view'=> $view,
    'admin'=> false,'close'=> false, 'save' => false,
    'delete'=> false
];

?>
<?php if($update || $delete): ?>
<div class="row">
    <div class="col-12">
        <span class="float-left">
            <?= $update ? Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']):'' ?>
            <?= $delete ?  Html::a('<i class="fas fa-times"></i> Eliminar', ['delete', 'id' => $model->Id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea eliminar este registro?',
                        'method' => 'post',
                    ],
                ]):'' ?>
        </span>
    </div>
</div>
<br/>
<?php endif; ?>
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
<?php
$js = <<< JS
   $(document).ready(function(){
        $("input, select, textarea").attr("disabled", true);
   });
JS;
$this->registerJs($js, View::POS_READY);
?>