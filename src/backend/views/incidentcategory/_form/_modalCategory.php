<?php
use yii\bootstrap4\Modal;
use common\customassets\helpers\Html;

/* @var $model \backend\models\Incidentcategory */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';

Modal::begin([
    'id' => $modalName,
    'title' => '<h4>Detalle de Categoría</h4>',
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => [
        'class' => 'bg-blue',
    ],
    'footer' => Html::button(Yii::t('app', '{icon} {action}', [
            'icon' => Html::icon('fas fa-save'),
            'action' => Yii::t('app', 'Save'),
        ]), ['class' => 'btn btn-success', 'id' => 'btn-save-cat']) . ""
        . Html::button(
            Yii::t('app', '{icon} {action}', [
                'icon' => Html::icon('fas fa-times-circle'),
                'action' => Yii::t('app', 'Cancel'),
            ])
            , [
            'class' => 'btn btn-danger',
            'id' => 'btn-cancel-cat',
            'data' => [ 'dismiss' => 'modal']])
]);
?>
<?= $this->render('_formCategory', ['model' => $model ]);?>
<?php Modal::end();