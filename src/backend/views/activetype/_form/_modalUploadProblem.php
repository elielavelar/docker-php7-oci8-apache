<?php
use yii\bootstrap4\Modal;

/* @var $model \backend\models\Problemtype */
$tableName = $model->tableName();
$modalName = $tableName.'-upload-modal';
Modal::begin([
    'title' => '<h4>Cargar Listado desde Archivo</h4>',
    'id' => $modalName ,
    'headerOptions' => [
        'class' => 'bg-blue',
    ],
    'toggleButton' => [
        'label' => '<i class="fa fa-upload"></i> Crear desde Archivo', 'class' => 'btn btn-primary',
    ],
]);
?>
<?= $this->render('_formUploadProblem', ['model' => $model ]);?>
<?php Modal::end();