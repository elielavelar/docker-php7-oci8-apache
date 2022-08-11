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
    'create' => false, 'update' => false, 'view'=> false,
    'admin'=> false,'close'=> false, 'save' => false,
    'delete'=> false
];

?>
<?php if($update): ?>
<div class="row">
    <div class="col-md-12">
        <span class="pull-left">
            <?= Html::a('<i class="fas fa-edit"></i> Actualizar', ['update', 'id' => $model->Id], ['class' => 'btn btn-primary']) ?>
        </span>
    </div>
</div>
<br/>
<?php endif; ?>
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
$js = <<< JS
   $(document).ready(function(){
        $("input, select, textarea").attr("disabled", true);
   });
JS;
$this->registerJs($js, View::POS_READY);
?>