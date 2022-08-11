<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\Type;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use backend\models\Option;
/* @var $this yii\web\View */
/* @var $model backend\models\Option */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = $tableName."-action-form";
$prefix = $tableName.'-action';
$types = ArrayHelper::map(Type::find()
        ->where(['KeyWord'=>  StringHelper::basename($model->className())])
        ->andFilterWhere(['in','Code',[Option::TYPE_ACTION,  Option::TYPE_PERMISSION]])
        ->select(['Id','Name'])
        ->all(),'Id','Name');
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
        <div class="col-3">
            <?= $form->field($model, 'Icon')->textInput(['maxlength' => true,'id'=>$prefix.'-icon']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),['id'=>$prefix.'-idstate']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'Sort')->input('number', ['id'=>$prefix.'-sort']); ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdType')->dropDownList($types,['id'=>$prefix.'-idtype']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'RequireAuth')->dropDownList([Option::REQUIRE_AUTH_FALSE =>'NO', Option::REQUIRE_AUTH_TRUE =>'SI'],['id'=>$prefix.'-requireauth']); ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'Require2StepAuth')->dropDownList([Option::REQUIRE_2STEP_AUTH_FALSE=>'NO', Option::REQUIRE_2STEP_AUTH_TRUE =>'SI'],['id'=>$prefix.'-require2stepauth'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'Url')->textInput(['maxlength' => true,'id'=>$prefix.'-url']) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'IdUrlType')->dropDownList($model->getUrlTypes(),['id'=>$prefix.'-idurltype'])?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'SaveLog')->dropDownList(['0'=>'NO','1'=>'SI'],['id'=>$prefix.'-savelog'])?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'SaveTransaction')->dropDownList(['0'=>'NO','1'=>'SI'],['id'=>$prefix.'-savetransaction'])?>
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
    <?= $form->field($model, 'IdParent')->hiddenInput(['id'=>$prefix.'-idparent'])->label(false); ?>
    <?= $form->field($model, 'ItemMenu')->hiddenInput(['id'=>$prefix.'-itemmenu'])->label(false); ?>
<?php ActiveForm::end(); ?>

