<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\sdms\DatosOper */
/* @var $form yii\widgets\ActiveForm */
?>

<?php $form = ActiveForm::begin([
        'id'=> 'datosoper-form'
    ]); ?>
<div class="card-body">
    
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'COD_OPER')->textInput(array_merge(['readonly'=> !$model->isNewRecord],['maxlength' => true ])) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'NOM1_OPER')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'NOM2_OPER')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <?= $form->field($model, 'APDO1_OPER')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-6">
            <?= $form->field($model, 'APDO2_OPER')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'COD_ROL')->dropDownList($model->getCodRols(),['prompt'=> '--Seleccione Rol--']) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'COD_CARGO_OPER')->dropDownList($model->getCargosOper(),['prompt' => '--Seleccione Cargo--']) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'COD_EMPLEADO')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'COD_CTRO_SERV')->dropDownList($model->getCtroServs(), ['prompt'=> '--Seleccione Centro de Servicio --']) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'STAT_OPER')->dropDownList(['A' => 'Activo', 'I' => 'Inactivo'],[]) ?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=> 'btn btn-danger'])?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>