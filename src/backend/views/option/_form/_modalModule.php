<?php
use yii\bootstrap4\Modal;
use kartik\helpers\Html;
/* @var $model \backend\models\Option */
$tableName = $model->tableName();
$modalName = $tableName.'-module-modal';

?>
<?php Modal::begin([
    'id' => $modalName,
    'size' => Modal::SIZE_LARGE,
    'title' => '<h3>'.Yii::t('app','Detail').' '.Yii::t('system', 'Module').'</h3>',
    'headerOptions' => ['class' => 'bg-primary'],
    'footer' => Html::button('<i class="fas fa-save"></i> '.Yii::t('app', 'Save'), ['class' => 'btn btn-success', 'id' => 'btn-save-module']) . ""
    . Html::button('<i class="fas fa-times-circle"></i> '.Yii::t('app', 'Cancel'), ['class' => 'btn btn-danger', 'id' => 'btn-cancel-module'])
]); 
?>
    <?=$this->render('_form', ['model'=>$model])?>
<?php Modal::end();?>