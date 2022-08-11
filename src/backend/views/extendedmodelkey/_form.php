<?php
use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\web\JsExpression;
use kartik\helpers\Html;

/*@var $model common\models\Extendedmodelkey */
/*@var $parentModel common\models\Extendedmodel */
/*@var $form ActiveForm */
/*@var $this yii\web\View */

$tableName = $model->tableName();
$formName = $tableName . '-form';

$expressionJS = <<< JS
    function(){
        var enable = $("#$tableName-usemask").is(':checked');
        if(enable){
            $("#$tableName-idinputmask").removeAttr('disabled');
            $("#$tableName-enabledcustommask").bootstrapSwitch('toggleDisabled',false,false);
        }  else {
            $("#$tableName-idinputmask").attr('disabled',true);
            $("#$tableName-enabledcustommask").prop('checked',false).trigger('change');
            $("#$tableName-enabledcustommask").bootstrapSwitch('toggleDisabled',true,true);
        }
    }
JS;
$customMaskJS = <<< JS
   function(){
        var usemaskenable = $("#$tableName-usemask").is(':checked');
        var enable = $("#$tableName-enabledcustommask").is(':checked'); 
        if(enable){
            $("#$tableName-idinputmask").val($("#$tableName-idinputmask").find('option:first').val()).attr('disabled',true);
            $("#$tableName-defaultmask").removeAttr('disabled').trigger('change').focus();
        }  else {
            usemaskenable ? $("#$tableName-idinputmask").removeAttr('disabled') : $("#$tableName-idinputmask").attr('disabled', true);
            $("#$tableName-defaultmask").val('').attr('disabled',true).trigger('change');
        }
   }
JS;

$resultValuesJS = <<< JS
    function (params){
        var form = {};
        form.ID = 'condition';
        form.SELECTOR = '.';
        form.PREFIX = '$tableName-';
        var data = getValuesBySelector(form);
        var model = {};
        model['Id'] =  $('#$tableName-idregistredmodelsource option:selected').val();
        model['keyAttribute'] = $('#$tableName-attributesourcename option:selected').val();
        $.extend(data, model);
        return data;
    }
JS;
$urlModel = Yii::$app->getUrlManager()->createUrl('registredmodel');
?>
<?php $form = ActiveForm::begin([
    'id' => $formName,
]); ?>
<div class="card-body">
    <?= $form->field($model, 'IdExtendedModel')->hiddenInput()->label(false);?>
    <?= $form->field($model, 'Id')->hiddenInput()->label(false);?>
    <?= Html::hiddenInput('tempvalue', null, ['id' => 'tempvalue'])?>
    <div class="row">
        <div class="col-4">
            <?= Html::label($model->getAttributeLabel('IdExtendedModel'),$tableName.'-modelname');?>
            <?= Html::label(($model->IdExtendedModel ? $model->extendedModel->registredModel->Name : ''),null, ['class' => 'form-control readonly','id' => $tableName.'-modelname']);?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'AttributeKeyName')->widget(Select2::className(),[
                        'data'=>$model->extendedModel->getModelAttributes(),
                        'disabled'=> (!$model->isNewRecord),
                        'initValueText'=> (($model->AttributeKeyName && $model->IdExtendedModel ) ? $model->extendedModel->getModelAttributeLabel($model->AttributeKeyName):""),
                        'options' => ['placeholder' => '--Seleccione Atributo--'],
                        'size'=> Select2::SIZE_MEDIUM,
                        'pluginOptions'=> [
                            'allowClear' => true,
                        ],
                    'pluginEvents'=> [
                        'change'=> "function(){ }",
                    ],
                ]);
            ?>
        </div>
        <div class="col-2">
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), [])?>
        </div>
        <div class='col-2'>
            <?= $form->field($model, 'EnabledModelSource')->widget(SwitchInput::class,[
                'options' => [
                    'id' => $tableName.'-enabledmodelsource',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => "function(){ adminsource(); }"
                ],
              ]) ?>
        </div>
    </div>
    <div class="row" style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? '':'display:none'?>">
        <div class='col-3'>
            <?=$form->field($model,'AttributeSourceName')->dropDownList($model->modelAttributes, ['prompt' => '-Seleccione Atributo-','id' => $tableName.'-attributesourcename'])?>
        </div>
    </div>
    <div class="row conditions" style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? '':'display:none'?>">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="active">Condiciones
                        <span class="float-right">
                            <?=Html::button('<i class="fas fa-plus"></i>',['tittle' =>'Agregar Condición', 'id' => 'btn-add-condition'])?>
                        </span>
                    </h5> 
                </div>
                <div class="card-body">
                    <?php foreach($model->conditionform as $cond){ ?>
                    <div class="row condition-row">
                        <?php foreach ($cond as $input):?>
                            <?=$input; ?>
                        <?php endforeach;?>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12 value" style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_DISABLED ? '':'display:none'?>">
            <?= $form->field($model, 'AttributeKeyValue')->textInput(['maxlength' => true, 'id' => $tableName.'-attributekeyvalue'])?>
        </div>
        <div class='col-12 value' style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? '':'display:none'?>">
            <?=$form->field($model, 'value')->widget(Select2::className(),[
                    'size'=> Select2::MEDIUM,
                    #'disabled'=> (!$model->isNewRecord),
                    'initValueText'=> $model->value,
                    'options'=> [
                        'placeholder'=> '--Seleccione Valor--',
                        'id' => $tableName.'-value'
                    ],
                    'pluginOptions'=> [
                        'allowClear'=>TRUE,
                        'minimunInputLength'=> 0,
                        'ajax' => [
                            'url'=> "$urlModel/getmodelvalues",
                            'dataType'=> 'json',
                            'method'=> 'post',
                            'data'=> new JsExpression($resultValuesJS),
                            'cache'=> true,
                            'delay'=> 0,
                        ],
                        'escapeMarkup'=>new JsExpression('function(markup){ return markup; }'),
                        'templateResult'=>new JsExpression('function(data){ return data.text; }'),
                        'templateSelection'=>new JsExpression('function(data){ return data.text; }'),
                    ],
                ]); 
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 4, 'maxlength' => true])?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?=Html::submitButton('<i class="fas fa-save"></i> Guardar',['class' => 'btn btn-success']); ?>
                <?=Html::a('<i class="fas fa-times"></i> Cancelar', ['extendedmodel/update','id' => $model->IdExtendedModel],['class' => 'btn btn-danger']);?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php
$js = <<< JS
   $(document).ready(function(){
       //$("#$tableName-enabledmodelsource").on('change', function(){
       //     adminsource();
       //});
        
        $("#btn-add-condition").on('click', function(){
            addmodelattributes();
        });
   });
JS;
$this->registerJs($js);

$script = <<< JS
    var adminsource = function(){
        var enabled = $('#$tableName-enabledmodelsource').is(':checked');
        if(enabled){
            $('#$tableName-idregistredmodelsource').parents('div.row').show();
            $('#$tableName-attributesourcename').parents('div.row').show();
            $('#$tableName-attributekeyvalue').parents('div.value').hide();
            $('#$tableName-value').parents('div.value').show();
            $('.conditions').show();
        } else {
            $('#$tableName-idregistredmodelsource').parents('div.row').hide();
            $('#$tableName-idregistredmodelsource').val('').trigger('change');
            $('#$tableName-attributesourcename').val('').trigger('change');
            $('#$tableName-attributekeyvalue').parents('div.value').show();
            $('#$tableName-value').parents('div.value').hide();
            $('.conditions').hide();
            $('.conditions').find('.condition-row').remove();
        }
    };
    
    var getmodelattributes = function(select){
        var id = $("#$tableName-idregistredmodelsource option:selected").val();
        var params = {};
        var data = {Id: id};
        params.URL = "$urlModel/getmodelattributes";
        params.DATA = data;
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            delete data['success'];
            $.each(data, function(i, value){
                selected = false;
                var opt = new Option(value, i, false, selected);
                select.append(opt);
            });
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
    
    var getAttributesModelSource = function(){
        var select = $("#$tableName-attributesourcename");
        select.find('option').not(':first').remove();
        getmodelattributes(select);
    };
    
    var addmodelattributes = function(){
        var id = $("#$tableName-idregistredmodelsource option:selected").val();
        var params = {};
        var data = {Id: id};
        var dvParent = $('div.conditions').find('div.card-body');
        var lastInput = dvParent.find('.condition-row:last-child').find('input[type=text]:last-child');
        var lastId = (lastInput !== "undefined" ) ? lastInput.attr('id') : null;
        $.extend(data, {'lastId' : lastId});
        params.URL = "$urlModel/getmodelattributesform";
        params.DATA = {'data':JSON.stringify(data)};
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var row = $("<div></div>");
            row.addClass('row').addClass('condition-row');
            row.append(data.input);
            dvParent.append(row);
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
    
    var removeCond = function(i){
        var id = '$tableName-condition-input-'+i;
        var e = $("#"+id);
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa Eliminar esta Condición?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: true
        },
        function(){
            e.parents('div.condition-row').remove();
            $("#$tableName-value").val('').trigger('change');
            $("#$tableName-AttributeKeyValue").val('').trigger('change');
        });
    };
JS;
$this->registerJs($script, $this::POS_HEAD);