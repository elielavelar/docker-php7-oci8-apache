<?php
use yii\bootstrap4\Modal;
use kartik\helpers\Html;
/* @var $model \backend\models\Option */
$tableName = $model->tableName();
$modalName = $tableName.'-group-modal';

?>
<?php Modal::begin([
    'id' => $modalName,
    'size' => Modal::SIZE_LARGE,
    'title' => '<h3>Detalle de Grupo</h3>',
    'headerOptions' => ['class' => 'bg-primary'],
    'footer' => Html::button('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success', 'id' => 'btn-save-group']) . ""
    . Html::button('<i class="fas fa-times-circle"></i> Cancelar', ['class' => 'btn btn-danger', 'id' => 'btn-cancel-group'])
]); 
?>
    <?=$this->render('_formGroup', ['model'=>$model])?>
<?php Modal::end();?>