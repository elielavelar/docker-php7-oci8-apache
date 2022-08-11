<?php
use yii\bootstrap4\Modal;
use yii\bootstrap4\Html;
use kartik\widgets\ActiveForm;
?>
<?php Modal::begin([
    'id' => $modalName,
    'title' => '<h3>Llave de Modelo</h3>',
    'headerOptions' => ['class' => 'bg-primary'],
    'footer' => Html::button('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success', 'id' => 'btn-save-alt']) . ""
    . Html::button('<i class="fas fa-times-circle"></i> Cancelar', ['class' => 'btn btn-danger', 'id' => 'btn-cancel-alt'])
]); 
?>
<?php $form = ActiveForm::begin([
    'id' => $formName
]); ?>
<?= $this->render('_formDetail', [
        'model' => $model, 'form' => $form, 'formName' => $formName, 'parentModel' => $parentModel
    ]) ?>
<?php ActiveForm::end();?>
<?php Modal::end();?>
