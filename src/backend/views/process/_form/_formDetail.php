<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Processdetail */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = $tableName."-form";

?>
<?php $form = ActiveForm::begin([
    'id' => $formName,'action'=>['processdetail/save'],'options'=>[
        'data-pjax' => '',
        'name'=>$formName,
        'enableAjaxValidation' => true,
        ],
]); ?>
<?= Html::activeHiddenInput($model, 'Id');?>
<?= Html::activeHiddenInput($model, 'IdProcess');?>
<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'IdServiceCentre')->widget(Select2::className(),[
                        'data'=>$model->getServicecentres(),
                        #'disabled'=> (!$model->isNewRecord),
                        'initValueText'=> ($model->IdServiceCentre ? $model->serviceCentre->Name:""),
                        'options' => ['placeholder' => '--Seleccione Departamento--'],
                        'size'=> Select2::SIZE_MEDIUM,
                        'pluginOptions'=> [
                            'allowClear' => true,
                        ],
                    'pluginEvents'=> [
                        'change'=> "function(){ }",
                    ],
            ]);
        ?>
    </div>
</div>
<div class="row">
    <div class="col-2">
        <?= $form->field($model, 'Enabled')->widget(SwitchInput::class,[
            'pluginOptions' => [
                'onText' => 'SI',
                'offText' => 'NO',
            ],
            'pluginEvents' => [],
        ]) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>