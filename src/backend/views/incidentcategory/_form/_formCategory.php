<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \backend\models\Incidentcategory */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$prefix = "det-".$tableName;
$formName = $tableName.'-det-form';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName
]); ?>

    <div class="card-body">
        <div  class="row">
            <div class="col-12">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => true, 'id' => "$prefix-name"]) ?>
            </div>
        </div>
        <div  class="row">
            <div class="col-4">
                <?= $form->field($model, 'Code')->textInput(['maxlength' => true, 'id' => "$prefix-code"]) ?>
            </div>
            <div class="col-4">
                <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(), [ 'id' => "$prefix-idtype"]) ?>
            </div>
            <div class="col-4">
                <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), [ 'id' => "$prefix-idstate"]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=>4, "id" => "$prefix-description"]) ?>
            </div>
        </div>
        <?= $form->field($model, 'IdParent')->hiddenInput(['id' => "$prefix-idparent"])->label(false) ?>
        <?= $form->field($model, 'Id')->hiddenInput(['id' => "$prefix-id"])->label(false) ?>
    </div>
<?php ActiveForm::end(); ?>