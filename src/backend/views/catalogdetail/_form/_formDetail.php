<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model \common\models\Catalogdetailvalue */
/* @var $form yii\widgets\ActiveForm */

$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName
]); ?>
    <div class="row">
        <div class="col-4">
           <?= $form->field($model, 'IdDataType')->dropDownList($model->getDataTypes(),[]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdValueType')->dropDownList($model->getValueTypes(),[]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?=$form->field($model, 'EnabledKeyWord')->dropDownList([$model::KEYWORD_DISABLED => 'No', $model::KEYWORD_ENABLED => 'Sí'],['id' => $tableName.'-enabledkeyword'])?> 
        </div>
        <div class="col-6 keyword" style="">
            <?= $form->field($model, 'KeyWord')->textInput([])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Value')->textarea(['row' => 4]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Sort')->textInput(['type'=> 'number']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput()->label(FALSE) ?>
    <?= $form->field($model, 'IdCatalogDetail')->hiddenInput()->label(FALSE) ?>
<?php ActiveForm::end(); ?>