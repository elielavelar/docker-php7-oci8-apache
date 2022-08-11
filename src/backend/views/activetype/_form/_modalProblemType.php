<?php
use yii\bootstrap4\Modal;
use common\customassets\helpers\Html;

/* @var $model \backend\models\Problemtype */
$tableName = $model->tableName();
$modalName = $tableName.'-modal';
Modal::begin([
    'title' => '<h4>Detalle de Problema</h4>',
    'id' => $modalName ,
    'size' => Modal::SIZE_LARGE,
    'headerOptions' => [
        'class' => 'bg-blue',
    ],
    'footer' => Html::button(Yii::t('app', '{icon} {action}', [
            'icon' => Html::icon('fas fa-save'),
            'action' => Yii::t('app', 'Save'),
        ]), ['class' => 'btn btn-success', 'id' => 'btn-save-problem']) . ""
        . Html::button(
            Yii::t('app', '{icon} {action}', [
                'icon' => Html::icon('fas fa-times-circle'),
                'action' => Yii::t('app', 'Cancel'),
            ])
            , [
            'class' => 'btn btn-danger',
            'id' => 'btn-cancel-problem',
            'data' => [ 'dismiss' => 'modal']])
]);
?>
<?= $this->render('_formProblemType', ['model' => $model ]);?>
<?php Modal::end();