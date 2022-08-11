<?php
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
/* @var $model \common\models\Extendedmodelfield */
$tableName = $model->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
?>
<?php Modal::begin([
    'id' => $modalName,
    'size' => Modal::SIZE_LARGE,
    'title' => '<h3>Detalle de Campo</h3>',
    'headerOptions' => ['class' => 'bg-primary'],
    'footer' => Html::button('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success', 'id' => 'btn-save-fld']) . ""
    . Html::button('<i class="fas fa-times-circle"></i> Cancelar', ['class' => 'btn btn-danger', 'id' => 'btn-cancel-fld'])
]); 
?>
<?php $form = ActiveForm::begin([
    'id' => $formName
]); ?>
<?= $this->render('_formField', [
        'model' => $model, 'form' => $form, 
    ]) ?>
<?php ActiveForm::end();?>
<?php Modal::end();?>
