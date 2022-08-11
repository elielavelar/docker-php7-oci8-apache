<?php
use yii\bootstrap4\Modal;

/* @var $model \common\models\Attachment */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';
?>
<?php Modal::begin([
    'title' => 'Carga de Archivos Adjuntos',
    'toggleButton' => [
        'label' => '<i class="fas fa-upload"></i> Carga de Archivos', 'class'=> 'btn btn-primary'
    ],
    'options'=> [
        'id'=> $modalName,
    ]
]); ?>
    <?= $this->render('_attachmentForm', [
    'model' => $model,
]) ?>
    <?php
Modal::end();
