<?php
use yii\bootstrap4\Modal;
use common\customassets\helpers\Html;

/* @var $model \backend\models\Activetype */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';

Modal::begin([
    'id' => $modalName,
    'title' => '<h4>Detalle de Tipo de Activo</h4>',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => [
        'class' => 'bg-blue',
    ],
    'footer' => Html::button(Yii::t('app', '{icon} {action}', [
            'icon' => Html::icon('fas fa-save'),
            'action' => Yii::t('app', 'Save'),
        ]), ['class' => 'btn btn-success', 'id' => 'btn-save-active']) . ""
        . Html::button(
            Yii::t('app', '{icon} {action}', [
                'icon' => Html::icon('fas fa-times-circle'),
                'action' => Yii::t('app', 'Cancel'),
            ])
            , [
            'class' => 'btn btn-danger',
            'id' => 'btn-cancel-active',
            'data' => [ 'dismiss' => 'modal']])
]);
?>
<?= $this->render('_formActiveType', ['model' => $model ]);?>
<?php Modal::end();