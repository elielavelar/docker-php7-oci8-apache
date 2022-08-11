<?php

use kartik\widgets\FileInput;
use yii\bootstrap4\Modal;
use kartik\helpers\Html;
use kartik\form\ActiveForm;

?>
<?php Modal::begin([
        'title' => 'Carga de Archivos Adjuntos',
        'toggleButton' => [
            'label' => '<i class="fas fa-upload"></i> Carga de Archivos', 'class'=> 'btn btn-primary'
        ],
       'options'=> [
           'id'=> 'modal-attachment'
       ]
    ]); ?>
    <?= $this->render('_attachmentForm', [
        'model' => $model,
    ]) ?>
    <?php 
Modal::end();
