<?php
use yii\bootstrap4\Modal;

/* @var $model \backend\models\Incidentresource */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';
?>
<?php Modal::begin([
    'title' => 'Recurso Relacionado',
    'options'=> [
        'id'=> $modalName,
        'tabindex' => false,
    ]
]); ?>
<?= $this->render('_resourceForm', [
    'model' => $model,
]) ?>
<?php
Modal::end();
