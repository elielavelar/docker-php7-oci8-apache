<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\Catalogdetailvalues */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
$frmName = 'form-'.$tableName;
?>
<?php $form = ActiveForm::begin([
    'id'=> $frmName
]); ?>
<div class="modal fade in" id="modal-<?=$tableName?>" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-md" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h3 class="modal-title" id="Label"><strong>Detalle de Valor</strong></h3>
            </div>
            <div class="modal-body">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                           <?= $form->field($model, 'IdDataType')->dropDownList($model->getDataTypes(),[]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'IdValueType')->dropDownList($model->getValueTypes(),[]) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'Value')->textInput([]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'Sort')->textInput(['type'=> 'number']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
                        </div>
                    </div>
                    <?= $form->field($model, 'Id')->hiddenInput()->label(FALSE) ?>
                    <?= $form->field($model, 'IdCatalogDetail')->hiddenInput()->label(FALSE) ?>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="pull-right">
                                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                                <?= Html::button('<i class="fas fa-times"></i> Cancelar', ['id'=>'btnCancel','class' => 'btn btn-danger']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>