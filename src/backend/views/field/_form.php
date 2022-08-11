<?php

use kartik\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model common\models\Field */
/* @var $modelSource common\models\Fieldcatalogsource */
/* @var $form yii\widgets\ActiveForm */
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
$url = Yii::$app->getUrlManager()->createUrl('field');
$urlSource = Yii::$app->getUrlManager()->createUrl('fieldcatalogmodelsource');
?>
<?php
$form = ActiveForm::begin([
            'id' => $formName,
        ]);
?>
<div class="card-body">
    <div class='row'>
        <div class='col-10'>
            <?= $form->field($model, 'Name')->textInput(['maxlength' => true, 'autocomplete' => 'off']) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-6'>
            <?= $form->field($model, 'KeyWord')->textInput(['maxlength' => true]) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'Code')->textInput(['maxlength' => true, 'disabled' => !$model->isNewRecord]) ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-4'>
            <?= $form->field($model, 'IdType')->dropDownList($model->getTypes(), ['prompt' => '-Seleccione Tipo-']) ?>
        </div>
        <div class='col-2'>
            <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), []) ?>
        </div>
        <div class='col-2'>
            <?=
            $form->field($model, 'MultipleValue')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName . '-multiplevalue',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => "function(){}"
                ],
            ])
            ?>
        </div>
        <div class='col-2'>
            <?=
            $form->field($model, 'HasCatalog')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName . '-hascatalog',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => "function(){"
                    . "var e = $('#$tableName-hascatalog').is(':checked');"
                    . " if(e){ "
                    . " $('#$tableName-combinationvalue').parents('div.value').show();"
                    . " $('#$tableName-enabledmodelsource').parents('div.value').show();"
                    . "} else {"
                    . " $('#$tableName-combinationvalue').parents('div.value').hide();"
                    . " $('#$tableName-enabledmodelsource').parents('div.value').hide();"
                    . " $('#$tableName-combinationvalue').prop('checked',false).trigger('change');"
                    . " $('#$tableName-enabledmodelsource').prop('checked',false).trigger('change');"
                    . ""
                    . "}"
                    . "}"
                ],
            ])
            ?>
        </div>
    </div>
    <div class='row'>
        <div class='col-2'>
            <?=
            $form->field($model, 'UseMask')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName.'-usemask',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => new JsExpression($expressionJS),
                ],
            ])
            ?>
        </div>
        <div class='col-2'>
            <?=
            $form->field($model, 'EnabledCustomMask')->widget(SwitchInput::class, [
                'disabled' => ($model->UseMask == $model::USE_MASK_DISABLED),
                'options' => [
                    'id' => $tableName.'-enabledcustommask',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => new JsExpression($customMaskJS),
                ],
            ])
            ?>
        </div>
        <div class='col-2'>
            <?= $form->field($model, 'IdInputMask')->dropDownList($model->getInputMasks(),['prompt' => '-Seleccione Máscara-','disabled' => ($model->UseMask != $model::USE_MASK_ENABLED || $model->EnabledCustomMask == $model::CUSTOM_MASK_ENABLED)]) ?>
        </div>
        <div class='col-4'>
            <?= $form->field($model, 'DefaultMask')->textInput(['maxlength' => true, 'disabled' => ($model->EnabledCustomMask == $model::CUSTOM_MASK_DISABLED)]) ?>
        </div>
    </div>
    <div class="row">
        <div class='col-2 value' style="<?=$model->HasCatalog == $model::HAS_CATALOG_TRUE ? '':'display:none'?>">
            <?=
            $form->field($model, 'CombinationValue')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName . '-combinationvalue',
                    'title' => 'Indica si el valor del campo será una combinación de valor de catálogo + valor abierto'
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                #'pluginEvents' => [
                #    'switchChange.bootstrapSwitch' => new JsExpression($expressionJS),
                #],
            ])
            ?>
        </div>
        <div class='col-2 value' style="<?=$model->HasCatalog == $model::HAS_CATALOG_TRUE ? '':'display:none'?>">
            <?=
            $form->field($model, 'EnabledModelSource')->widget(SwitchInput::class, [
                'options' => [
                    'id' => $tableName . '-enabledmodelsource',
                ],
                'pluginOptions' => [
                    'onText' => 'Sí',
                    'offText' => 'No',
                ],
                'pluginEvents' => [
                    'switchChange.bootstrapSwitch' => "function(){ adminsource(); }"
                ],
            ])
            ?>
        </div>
    </div>
    <div class="row conditions" style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? '':'display:none'?>">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="active">Modelos</h5>
                </div>
                <div class="card-body modelsource-container">
                    <div class="row select-modelsource">
                        <div class="col-4">
                            <?=Select2::widget([
                                'name' => $tableName.'-dynamicmodel',
                                'data' => $modelSource->getModels(),
                                'options' => [
                                    'id' => $tableName.'-dynamicmodel',
                                    'placeholder' => '--Seleccione Modelo--',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'templateSelection'=>new JsExpression("function(data){ return data.text; }"),
                                ],
                                'pluginEvents' => [
                                    "change" => "function() { var i = $('#$tableName-dynamicmodel').val(); $('#btn-add-model').attr('disabled',(i === '')); }",
                                ],
                                'addon' => [
                                    'append' => [
                                        'content' => Html::button('<i class="fas fa-plus"></i>',[ 'class' => 'btn btn-primary btn-sm', 'id' => 'btn-add-model', 'data-toggle' => 'tooltip', 'disabled' => true]),
                                        'asButton' => true
                                    ]
                                ],
                            ]); ?>
                        </div>
                    </div>
                    <?php foreach($model->conditionform as $source){ ?>
                        <div class="row modelsource-row sortable" id="model_<?=$source['Id'];?>">
                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-12">
                                            <?=$source['label'];?>
                                            <?=$source['input'];?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($source['conditions'] as $cond){ ?>
                                    <div class="row condition-row">
                                        <?php foreach ($cond as $input):?>
                                            <?=$input; ?>
                                        <?php endforeach;?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row"></div>
    <div class="row" style="margin-top: 15px">
        <div class='col-3 value' style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? '':'display:none'?>">
            <?=$form->field($model,'AttributeSourceName')->dropDownList($model->modelAttributes, ['prompt' => '-Seleccione Atributo-','id' => $tableName.'-attributesourcename'])?>
        </div>
        <div class="col-4 value" style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_DISABLED ? '':'display:none'?>">
            <?= $form->field($model, 'Value')->textInput(['maxlength' => true, 'id' => $tableName.'-value'])?>
        </div>
        <div class='col-4 value' style="<?=$model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? '':'display:none'?>">
            <?=$form->field($model, 'customvalue')->widget(Select2::className(),[
                    'size'=> Select2::MEDIUM,
                    #'disabled'=> (!$model->isNewRecord),
                    'initValueText'=> $model->customvalue,
                    'options'=> [
                        'placeholder'=> '--Seleccione Valor--',
                        'id' => $tableName.'-customvalue'
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
    <div class='row'>
        <div class='col-12'>
            <?= $form->field($model, 'Description')->textarea(['rows' => 4]) ?>
        </div>
    </div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'], ['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php 
$js = <<< JS
    $(document).ready(function(){
        $("#btn-add-modelsource").on('click', function(){
            addmodelsource();
        });
        
        $("#btn-add-condition").on('click', function(){
            addmodelattributes();
        });
        
        $("#btn-add-model").on('click', function(){
            addmodelsource();
        });
    });
JS;
$this->registerJS($js);
       
$jsHead = <<< JS
    var adminsource = function(){
        var enabled = $('#$tableName-enabledmodelsource').is(':checked');
        $('#$tableName-dynamicmodel').val('').trigger('change');
        if(enabled){
            $('#$tableName-value').parents('div.value').hide();
            $('#$tableName-customvalue, #$tableName-attributesourcename').parents('div.value').show();
            $('.conditions').show();
        } else {
            $('#$tableName-attributesourcename').val('').trigger('change');
            $('#$tableName-value').parents('div.value').show();
            $('#$tableName-customvalue').val('').trigger('change');
            $('#$tableName-customvalue, #$tableName-attributesourcename').parents('div.value').hide();
            $('.conditions').hide();
            $('.conditions').find('.condition-row').remove();
        }
    };
        
    var addmodelsource = function(){
        var model = $("#$tableName-dynamicmodel").val();
        var opt = $("#$tableName-dynamicmodel").find('option[value='+model+']');
        var val = opt.attr('value');
        var label = opt.html();
        var container = $('div.conditions').find('div.modelsource-container');
        var dvParent = $('div.modelsource-row:last-child').find('div.card-body');
        var lastInput = dvParent.find('.condition-row:last-child').find('input[type=text]:last-child');
        var lastId = (lastInput !== "undefined" ) ? lastInput.attr('id') : null;
        var data = {IdField: '$model->Id','Id':val};
        $.extend(data, {'lastId' : lastId});
        params = {};
        params.URL = "$urlSource/getmodelattributesform";
        params.DATA = {'data':JSON.stringify(data)};
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var divContainer = $("<div></div>");
            divContainer.addClass('row').addClass('modelsource-row').addClass('sortable');
            divContainer.append(data.container);
            container.append(divContainer);
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
$this->registerJs($jsHead, yii\web\View::POS_HEAD);
?>