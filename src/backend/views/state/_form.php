<?php

use kartik\helpers\Html;
use kartik\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\State */
/* @var $form yii\widgets\ActiveForm */

$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<?php $form = ActiveForm::begin([
        'id' => $formName
]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-4'>
          <?= $form->field($model, 'KeyWord')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-4'>
          <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-2'>
          <?= $form->field($model, 'Value')->textInput(['maxlength' => true]) ?>
      </div>
      <div class='col-2'>
          <?= $form->field($model, 'Sort')->input('number', []) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-12'>
          <?= $form->field($model, 'Description')->textarea(['maxlength' => true, 'rows' => 4]) ?>
      </div>
  </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i>Guardar', [
                        'class' => 'btn btn-success',
                        'data' => [
                            'confirm' => '¿Está seguro que desea Guardar este Registro?',
                            'method' => 'post'
                        ]
                    ]) ?>
                <?=Html::a('<i class="fas fa-times"></i> Cancelar', ['index'], ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => '¿Está seguro que desea Salir de este Registro?',
                    ]])?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
