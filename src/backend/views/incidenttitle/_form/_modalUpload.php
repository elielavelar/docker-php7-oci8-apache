<?php
use yii\bootstrap4\Modal;

/* @var $model \common\models\Attachment */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';
?>
<?php Modal::begin([
    'title'=>'<h4>Cargar Listado desde Archivo</h4>',
    'headerOptions'=>[
        'class'=>'bg-blue',
    ],
    'toggleButton'=>[
        'label'=>'<i class="fa fa-upload"></i> Crear desde Archivo','class'=>'btn btn-primary',
    ],
]);
; ?>
<?= $this->render('_attachmentForm', [
    'model' => $model,
]) ?>
<?php
Modal::end();
?>