<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;
use kartik\widgets\SwitchInput;

/* @var $this yii\web\View */
/* @var $model common\models\Company */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
?>
<?php $form = ActiveForm::begin([]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-6'>
          <?= $form->field($model, 'TradeName')->textInput(['maxlength' => true]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-4'>
          <?= $form->field($model, 'TaxRegistrationNumber')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-4'>
          <?= $form->field($model, 'TaxIdentificationNumber')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-3'>
          <?= $form->field($model, 'IdSizeType')->dropDownList($model->getSizeTypes(),[])?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'BusinessSector')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-3'>
            <?= $form->field($model, 'Enabled')->widget(SwitchInput::class,[
                  'options' => [
                      'id' => $tableName.'-enabled',
                  ],
                  'pluginOptions' => [
                      'onText' => 'Sí',
                      'offText' => 'No',
                  ]
              ]) ?>
        </div>
  </div>
  <div class='row'>
      <div class='col-12'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
      </div>
  </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'],['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
