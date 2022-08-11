<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Activetype */
/* @var $form yii\widgets\ActiveForm */
$url = Yii::$app->getUrlManager()->createUrl('incidentcategory');

?>

<?php $form = ActiveForm::begin(); ?>
<div class="card-body">
    <div class="row">
        <div class="col-8">
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=>6]) ?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton("<i class='fas fa-save'></i> Guardar", ["class" => 'btn btn-success']) ?>
                <?= Html::a("<i class='fas fa-times'></i> Cancelar", ["incidentcategory/update",'id'=> $model->IdCategoryType],["class" => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>