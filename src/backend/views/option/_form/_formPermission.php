<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model backend\models\Option */
/* @var $form yii\widgets\ActiveForm */
$frmName = $model->tableName()."_permission-form";
$fieldPrefix = 'permission-'.$model->tableName()."-";

?>

<?php $form = ActiveForm::begin([
    'id'=>$frmName,
]); ?>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true,'id'=>$fieldPrefix.'name']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'KeyWord')->textInput(['maxlength' => true,'id'=>$fieldPrefix.'keyword']) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'Icon')->textInput(['maxlength' => true,'id'=>$fieldPrefix.'icon']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),['id'=>$fieldPrefix.'idstate']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'Sort')->input('number',['id'=>$fieldPrefix.'sort']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-9">
            <?= $form->field($model, 'Url')->textInput(['maxlength' => true,'id'=>$fieldPrefix.'url']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdUrlType')->dropDownList($model->getUrlTypes(),['id'=>$fieldPrefix.'idurltype'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6,'id'=>$fieldPrefix.'description']) ?>
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
                'id'=>$fieldPrefix.'Optionenvironment'
            ]); ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput(['id'=>$fieldPrefix.'id'])->label(FALSE); ?>
    <?= $form->field($model, 'IdParent')->hiddenInput(['id'=>$fieldPrefix.'idparent'])->label(FALSE); ?>
    <?= $form->field($model, 'ItemMenu')->hiddenInput(['id'=>$fieldPrefix.'itemmenu'])->label(FALSE); ?>
    <?= $form->field($model, 'IdType')->hiddenInput(['id'=>$fieldPrefix.'idtype'])->label(FALSE); ?>
    <?= $form->field($model, 'RequireAuth')->hiddenInput(['id'=>$fieldPrefix.'requireauth'])->label(FALSE); ?>
    <?= $form->field($model, 'Require2StepAuth')->hiddenInput(['id'=>$fieldPrefix.'require2stepauth'])->label(FALSE); ?>
<?php ActiveForm::end(); ?>

