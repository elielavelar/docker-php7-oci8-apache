<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\Catalogversion */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
?>
<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'Version')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'CurrentVersion')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName.'-currentversion',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ]
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>    

    <?= $form->field($model, 'IdCatalog')->hiddenInput()->label(FALSE)?>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Yii::$app->customFunctions->userCan('catalogCreate') ? Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']):'' ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['catalog/update','id'=>$model->IdCatalog],['class'=>'btn btn-danger']);?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
