<?php
use yii\bootstrap4\Modal;
use kartik\helpers\Html;
/* @var $model \common\models\Personaldocument */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';

?>
<?php Modal::begin([
    'id' => $modalName,
    'size' => Modal::SIZE_SMALL,
    'title' => '<h3>Detalle de Documento</h3>',
    'headerOptions' => ['class' => 'bg-primary'],
    'footer' => Html::button('<i class="fas fa-save"></i> Guardar', ['type' => 'button','class' => 'btn btn-success', 'id' => 'btn-save-alt']) . ""
        . Html::button('<i class="fas fa-times-circle"></i> Cancelar', ['class' => 'btn btn-danger', 'id' => 'btn-cancel-alt'])
]);
?>
<?=$this->render('_formDetail', ['model'=>$model])?>
<?php Modal::end();?>