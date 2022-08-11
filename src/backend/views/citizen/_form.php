<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model frontend\models\Citizen */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->getTableName();
$formName = $tableName."-form";
?>
<?php $form = ActiveForm::begin([
        'id'=>$formName,
]); ?>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'LastName')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'Email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'Telephone')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'CreateDate')->textInput(['disabled'=>TRUE]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'UpdateDate')->textInput(['disabled'=>TRUE]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),[]) ?>
            </div>
        </div>
    </div>
    <div class="panel-footer">
        <div class="row">
            <div class="pull-left">
                <?= (Yii::$app->user->can('citizenSendemailconfirmation') && !$model->isNewRecord) ? Html::a('Enviar Confirmación Usuario Correo', ['sendemailconfirmation','id'=> $model->Id] ,['class' => 'btn btn-warning']):"";?>
            </div>
            <div class="pull-right">
                <?= Html::submitButton('Actualizar', ['class' =>'btn btn-primary']) ?>
                <?= Html::a('Cancelar', ['index'] ,['class' => 'btn btn-danger']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

