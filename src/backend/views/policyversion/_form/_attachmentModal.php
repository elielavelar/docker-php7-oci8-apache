<?php
use yii\bootstrap4\Modal;

/* @var $model \common\models\Attachment */
?>
<?php Modal::begin([
        'title' => 'Carga de Archivos Adjuntos',
        'options'=> [
           'id'=> 'modal-attachment'
        ]
    ]); ?>
    <?= $this->render('_attachmentForm', [
        'model' => $model,
    ]) ?>
    <?php 
Modal::end();
