<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentcaterogy */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$formName = $tableName.'-form';
?>
<?php $form = ActiveForm::begin([
    'id'=> $formName
]); ?>
<div class="modal fade in" id="modal-category" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h3 class="modal-title" id="Label"><strong>Detalle de Categor&iacute;a</strong></h3>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div  class="row">
                        <div class="col-12">
                            <?= $form->field($model, 'Name')->textInput(['maxlength' => true]) ?>
                        </div>
                    </div>
                    <div  class="row">
                        <div class="col-6">
                            <?= $form->field($model, 'Code')->textInput() ?>
                        </div>
                        <div class="col-6">
                            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= $form->field($model, 'Description')->textarea(['maxlength' => true,'rows'=>4]) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'IdParent')->hiddenInput()->label(FALSE) ?>
                    <?= $form->field($model, 'Id')->hiddenInput()->label(FALSE) ?>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <div class="col-12">
                        <span class="float-right">
                            <?= Html::submitButton('<i class="fa fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                            <button type="button" id="btnCancel" name="btnCancel" class="btn btn-danger">
                                <i class="fa fa-times"></i> Cancelar
                            </button>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


