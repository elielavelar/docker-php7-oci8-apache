<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Option */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = $tableName."-group-form";
$prefix = $tableName.'-group';
?>

<?php $form = ActiveForm::begin([
    'id'=>$formName,
]); ?>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true,'id'=>$prefix.'-name']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'KeyWord')->textInput(['maxlength' => true,'id'=>$prefix.'-keyword']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'Icon')->textInput(['maxlength' => true,'id'=>$prefix.'-icon']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),['id'=>$prefix.'-idstate']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'Sort')->textInput(['id'=>$prefix.'-sort']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'ItemMenu')->dropDownList([ $model::ITEMMENU_DISABLED =>'NO', $model::ITEMMENU_ENABLED =>'SI'],['id'=>$prefix.'-itemmenu']); ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'RequireAuth')->dropDownList([ $model::REQUIRE_AUTH_FALSE =>'NO', $model::REQUIRE_AUTH_TRUE =>'SI'],['id'=>$prefix.'-requireauth']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            <?= $form->field($model, 'Url')->textInput(['maxlength' => true,'id'=>$prefix.'-url']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdUrlType')->dropDownList($model->getUrlTypes(),['id'=>$prefix.'-idurltype'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6,'id'=>$prefix.'-description']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Optionenvironment')->checkboxList($model->getEnvTypes(),[
                'class'=>'form-control checkbox-list',
                'tag'=>"span",
                'item'=> function($index, $label, $name, $checked, $value){
                    return Html::checkbox($name, $checked, [
                        'value' => $value,
                        'label' => $label,
                        'class' => 'checkbox-list',
                     ]);
                },
                'id'=>$prefix.'-optionenvironment'
            ]); ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput(['id'=>$prefix.'-id'])->label(false); ?>
    <?= $form->field($model, 'IdType')->hiddenInput(['id'=>$prefix.'-idtype'])->label(false); ?>
    <?= $form->field($model, 'IdParent')->hiddenInput(['id'=>$prefix.'-idparent'])->label(false); ?>
<?php ActiveForm::end(); ?>