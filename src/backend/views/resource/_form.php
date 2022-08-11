<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Resource */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdType')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Code')->textInput(['maxlength' => true]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdResourceType')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdServiceCentre')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdState')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'CreationDate')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdUserCreation')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'LastUpdateDate')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdUserLastUpdate')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdParent')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
      </div>
  </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton(Yii::t('system', 'Guardar'), ['class' => 'btn btn-success']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
