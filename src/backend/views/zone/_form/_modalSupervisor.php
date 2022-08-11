<?php

use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\web\View;
/* @var $model common\models\Zonesupervisors */

?>
<?php Modal::begin([
    'id' => $modalName,
    'title' => '<h3>Registrar Supevisor</h3>',
    'size' => Modal::SIZE_DEFAULT,
    'headerOptions' => ['class' => 'bg-primary'],
    'footer' => Html::button('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success', 'id' => 'btnDetSave']) . ""
    . Html::button('<i class="fas fa-times-circle"></i> Cancelar', ['class' => 'btn btn-danger', 'id' => 'btnDetCancel'])
]); 
?>
<?= $this->render('_formSupervisor', [
        'model' => $model, 'formName' => $formName, 'tableName' => $tableName,
    ]) ?>
<?php Modal::end();?>
