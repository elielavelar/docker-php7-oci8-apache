<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \backend\models\Incidentcategory */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = 'form-'.$tableName;
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName
]); ?>

<div class="card-body">
    <div  class="row">
        <div class="col-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <?php if($model->IdParent): ?>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <?= Html::label( $model->getAttributeLabel('IdParent'), 'nom-parent', [ 'class' => 'control-label']) ?>
                    <?= Html::label( $model->IdParent ? $model->parent->Name:'', null, [ 'class' => 'form-control disabled']) ?>
                </div>
            </div>
        </div>
    <?php endif;?>
    <div  class="row">
        <div class="col-4">
            <?= $form->field($model, 'Code')->textInput() ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(), []) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=>4]) ?>
        </div>
    </div>
    <?= $form->field($model, 'IdParent')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'Id')->hiddenInput()->label(false) ?>
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fa fa-save"></i> '.Yii::t('app','Save'), ['class' => 'btn btn-success']) ?>
                <?= Html::a("<i class='fas fa-times'></i> ".Yii::t('app','Cancel'), ['index'], ['class'=>'btn btn-danger'])?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>