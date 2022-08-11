<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidenttitle */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $form = ActiveForm::begin([]); ?>
<div class="card-body">
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Title')->textInput(['maxlength' => true]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'IdCategoryType')->textInput() ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
      </div>
  </div>
  <div class='row'>
      <div class='col-6'>
          <?= $form->field($model, 'Enabled')->textInput() ?>
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
