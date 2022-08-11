<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Activetype */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName
]); ?>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(),[]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=> 4]) ?>
        </div>
    </div>
    <?= $form->field($model, 'Id')->hiddenInput()->label(FALSE) ?>
    <?= $form->field($model, 'IdCategoryType')->hiddenInput()->label(FALSE) ?>

<?php ActiveForm::end(); ?>