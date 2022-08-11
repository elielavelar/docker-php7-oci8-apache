<?php
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\helpers\Html;
use yii\web\View;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* @var $this yii\web\View*/
/* @var $model \app\modules\admin\models\BackupSchema */

/**
 * Description of _form
 *
 * @author Eliel Avelar <ElielAbisai.AvelarJaimes@muehlbauer.de>
 */
$tableName = $model->getModel()."schema";
$formName = $tableName."-form";

$options = [];
if($model->complete){
    $options["disabled"]=TRUE;
}

    $form = ActiveForm::begin([
        'id'=>$formName,
    ]);
?>
<div class="panel-body">
    <div class="row">
        <div class="col-lg-4">
            <?=$form->field($model, 'complete')->checkbox([]);?>
        </div>
        <div class="col-lg-4">
            <?=$form->field($model, 'enableZip')->checkbox($options);?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2">
            <?=$form->field($model, 'structure')->checkbox($options);?>
        </div>
        <div class="col-lg-2">
            <?=$form->field($model, 'data')->checkbox($options);?>
        </div>
        <div class="col-lg-2">
            <?=$form->field($model, 'includeKeys')->checkbox($options);?>
        </div>
        <div class="col-lg-2">
            <?=$form->field($model, 'includeDBName')->checkbox($options);?>
        </div>
        <div class="col-lg-2">
            <?=$form->field($model, 'addcheck')->checkbox($options);?>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <?=  Html::label('Seleccionar: ', $tableName."-tablename");?>
            <?= Html::a('Todos', 'javascript:selectItem(1)', []);?>, 
            <?= Html::a('Ninguno', 'javascript:selectItem(0)', []);?>
            <?= $form->field($model, 'tableName')->multiselect($model->tables,$options);?>
        </div>
    </div>
</div>
<div class="panel-footer">
    <div class="row">
        <div class="col-lg-12">
            <span class="pull-right">
                <?= Html::button('<i class="fa fa-save"></i> Crear',['id'=>'btnSave','class'=>'btn btn-success',]);?>
                <?= Html::a('<i class="fa fa-ban"></i> Cancelar', ['index'], ['class'=>'btn btn-danger']);?>
            </span>
        </div>
    </div>
</div>
<?php
    ActiveForm::end();
?>

<?php
$script = <<< JS
   $(document).ready(function(){
        
        $("#btnSave").on('click', function(){
            swal({
                title: "Confirmación de Respaldo",
                text: "¿Está Seguro que desea Realizar el Respaldo?",
                type: "info",
                showCancelButton: true,
                confirmButtonColor: "#006030",
                confirmButtonText: "Sí",
                cancelButtonText: "Cancelar",
                closeOnConfirm: false
              },
              function(){
                    $("#$formName").submit();
              });
        });
        
        $("#$tableName-complete").on('click', function(){
            var com = $(this).is(':checked');
            if(com){
                disabledForm();
            } else {
                enabledForm();
            }
        });
   });
JS;
$script = $model->complete ? $script." disabledMultiple();":$script;
$this->registerJs($script, View::POS_READY);

$JS = <<< SP
   var disabledMultiple = function(){
       $(".input-multiselect").find("input[type=checkbox]").attr("disabled",true);
   }; 
        
   var enabledForm = function(){
       var frm = $("#$formName input[type=checkbox]");
       frm.not('#$tableName-complete')
           .prop("checked",true)
           .attr("checked",true)
           .removeAttr('disabled');
        
       frm.not('#$tableName-complete').parents('div.checkbox').removeClass('disabled');
   }; 
   
   var disabledForm = function(){
       var frm = $("#$formName input[type=checkbox]");
       frm.not('#$tableName-complete')
           .prop("checked",true)
           .attr('checked',true)
           .attr('disabled',true);
       frm.not('#$tableName-complete').parents('div.checkbox').addClass('disabled');
   }; 
        
    var selectItem = function(value){
        var complete = $('#$tableName-complete').is(":checked");
        if(value == 1 && !complete){
            var frm = $("#backupschema-tablename input[type=checkbox]");
            frm.prop("checked",true)
                .attr('checked',true);
        } else if(!complete){
            var frm = $("#backupschema-tablename input[type=checkbox]");
            frm.removeProp("checked")
                .removeAttr('checked');
        }
    };
   
SP;
$this->registerJs($JS, View::POS_HEAD);
?>