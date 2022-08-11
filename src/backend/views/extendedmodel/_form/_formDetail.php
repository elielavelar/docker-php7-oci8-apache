<?php
use kartik\form\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\SwitchInput;
use yii\web\JsExpression;
use kartik\helpers\Html;

/*@var $model common\models\Extendedmodelkeys */
/*@var $parentModel common\models\Extendedmodels */
/*@var $form ActiveForm */
/*@var $this yii\web\View */
$tableName = $model->tableName();
$formName = $tableName.'-form';

$resultValuesJS = <<< JS
    function (params){
        var form = {};
        form.ID = 'condition';
        form.SELECTOR = '.';
        form.PREFIX = '$tableName-';
        var data = getValuesBySelector(form);
        var model = {};
        //model['Id'] =  $('#$tableName-idregistredmodelsource option:selected').val();
        model['keyAttribute'] = $('#$tableName-attributesourcename option:selected').val();
        $.extend(data, model);
        return data;
    }
JS;
$url = Yii::$app->getUrlManager()->createUrl('registredmodel');
$enabled = $model::MODEL_SOURCE_ENABLED;
?>
<?= $form->field($model, 'IdExtendedModel')->hiddenInput()->label(false);?>
<?= $form->field($model, 'Id')->hiddenInput()->label(false);?>
<?= Html::hiddenInput('tempvalue', null, ['id' => 'tempvalue'])?>
<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'AttributeKeyName')->widget(Select2::className(),[
                    'data'=>$parentModel->getModelAttributes(),
                    #'disabled'=> (!$model->isNewRecord),
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
</div>
<div class="row">
    <div class="col-6">
        <?= $form->field($model, 'IdState')->dropDownList($model->getStates(), [])?>
    </div>
    <div class='col-6'>
        <?=$form->field($model, 'EnabledModelSource')->dropDownList([$model::MODEL_SOURCE_DISABLED => 'No', $model::MODEL_SOURCE_ENABLED => 'Sí'],['id' => $tableName.'-enabledmodelsource'])?> 
    </div>
</div>
<div class="row" style="display: none">
    <div class='col-6'>
        <?=$form->field($model,'AttributeSourceName')->dropDownList([], ['prompt' => '-Seleccione Atributo-','id' => $tableName.'-attributesourcename'])?>
    </div>
</div>
<div class="row conditions" style="display: none">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="active">Condiciones
                    <span class="float-right">
                        <?=Html::button('<i class="fas fa-plus"></i>',['tittle' =>'Agregar Condición', 'id' => 'btn-add-condition'])?>
                    </span>
                </h5> 
            </div>
            <div class="card-body"></div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 value">
        <?= $form->field($model, 'AttributeKeyValue')->textInput(['maxlength' => true, 'id' => $tableName.'-attributekeyvalue'])?>
    </div>
    <div class='col-12 value' style="display:none">
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
                        'url'=> "$url/getmodelvalues",
                        'dataType'=> 'json',
                        'method'=> 'post',
                        'data'=> new JsExpression($resultValuesJS),
                        'cache'=> true,
                        'delay'=> 0,
                    ],
                    'escapeMarkup'=>new JsExpression('function(markup){ return markup; }'),
                    'templateResult'=>new JsExpression('function(data){ return data.text; }'),
                    'templateSelection'=>new JsExpression("function(data){ "
                            . "var enabled = $('#$tableName-enabledmodelsource option:selected').val();"
                            . "enabled === '$enabled'? $('#$tableName-attributekeyvalue').val(data.text) : null;"
                            . "return data.text; }"),
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
<?php
$jsReady = <<< JS
   $(document).ready(function(){
        
   });
JS;
$this->registerJs($jsReady);

$jsHead = <<< JS
    
JS;
$this->registerJs($jsHead, $this::POS_HEAD);
?>