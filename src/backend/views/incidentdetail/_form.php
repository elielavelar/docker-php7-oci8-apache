<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\DateTimePicker;
use kartik\widgets\Select2;
use common\customassets\fileinput\FileInput;

/* @var $this yii\web\View */
/* @var $model backend\models\Incidentdetail */
/* @var $form yii\widgets\ActiveForm */

$controller = 'incident';
$admin = Yii::$app->customFunctions->userCan($controller.'Admin');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$mainController = Yii::$app->controller->id;
$urlMainController = Yii::$app->getUrlManager()->createAbsoluteUrl($mainController);
$urlIncidentCategory = Yii::$app->getUrlManager()->createAbsoluteUrl('incidentcategory');
$urlActiveTypes = Yii::$app->getUrlManager()->createAbsoluteUrl('activetype');
$urlProblemTypes = Yii::$app->getUrlManager()->createAbsoluteUrl('problemtype');

$tableName = $model->tableName();
$formName = $tableName.'-form';
$className = \yii\helpers\StringHelper::basename(get_class( $model ));
$csrfParam = Yii::$app->getRequest()->csrfParam;
$idCategory = $model->IdCategoryType;
$idSubCategory = $model->IdSubCategoryType;
$idActiveType = $model->IdActiveType;
$idProblemType = $model->IdProblemType;
$enabled = $model->EnableReclasification;

$attributes = \yii\helpers\Json::encode([ 'results' => $model->getAttributesByActivity()]);
?>
<?php $form = ActiveForm::begin([
        'id' => $formName
]); ?>
<div class="card-body">
    <div class="row">
        <div class="col-4">
            <?= Html::label('Ticket', 'NumTicket')?>
            <?= Html::label($model->incident->Ticket, NULL, ['class'=>'form-control readonly'])?>
        </div>
        <div class="col-4">
            <?= ($admin || $model->isNewRecord) ?
                $form->field($model, 'RecordDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => Yii::t('system', 'Record Date')],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'minuteStep' => 1,
                    ],
                    'pluginEvents' => [
                        'changeDate' => "function(e){  }",
                    ],
                ])
                : $form->field($model, 'RecordDate')->textInput(['readonly' => true]);
            ?>
        </div>
        <div class="col-4">
            <?= ($admin || $model->isNewRecord) ?
                $form->field($model, 'DetailDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => Yii::t('system', 'Detail Date')],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'minuteStep' => 1,
                    ],
                    'pluginEvents' => [
                        'changeDate' => "function(e){  }",
                    ],
                ])
                : $form->field($model, 'DetailDate')->textInput(['readonly' => true]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'IdActivityType')->dropDownList($model->getActivityTypes(),[
                    'prompt' => '--'.Yii::t('system', 'Select Activity').'--'
            ]) ?>
        </div>
        <div class="col-2">
            <?= $form->field($model, 'EnableReclasification')->dropDownList( $model->getEnableReclasifitionTypes(), [

            ]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdCategoryType')->dropDownList($model->getCategoryTypes(),
                [
                    'prompt'=>'--Seleccione Tipo de Incidencia--'
                ]) ?>
        </div>
        <div class="col-3">
            <?php
            if($admin){
                echo $form->field($model, 'IdSubCategoryType')->dropDownList( $model->getSubCategoryTypes(),[
                    'prompt'=>'--Seleccione Sub Tipo de Incidencia--'
                ]);
            } else {
                echo Html::label($model->getAttributeLabel('IdSubCategoryType'), "$tableName-idsubcategorytype");
                echo Html::label(($model->IdSubCategoryType ? $model->subCategoryType->Name :""), null, ['class'=>'form-control readonly']);
                echo $form->field($model, 'IdSubCategoryType')->hiddenInput()->label(false);
            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Description')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= ($admin || $model->isNewRecord) ?
                $form->field($model, 'SolutionDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => Yii::t('system', 'Solution Date')],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'minuteStep' => 1,
                    ],
                    'pluginEvents' => [
                        'changeDate' => "function(e){  }",
                    ],
                ])
                : $form->field($model, 'SolutionDate')->textInput(['readonly' => true]);
            ?>
        </div>
        <div class="col-4">
            <?= ($admin || $model->isNewRecord) ?
                $form->field($model, 'OnSiteDate')->widget(
                    DateTimePicker::class, [
                    'language' => 'es',
                    'readonly' => true,
                    'options' => ['placeholder' => Yii::t('system', 'On Site Date')],
                    'pluginOptions' => [
                        'format' => 'dd-mm-yyyy hh:ii',
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'minuteStep' => 1,
                    ],
                    'pluginEvents' => [
                        'changeDate' => "function(e){  }",
                    ],
                ])
                : $form->field($model, 'OnSiteDate')->textInput(['readonly' => true]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?= $form->field($model, 'IdSupportType')->dropDownList( $model->getSupportTypes(), [
                    'prompt' => '--'.Yii::t('system','Select Support Type').'--'
            ]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdActiveType')->dropDownList( $model->getActiveTypes(), [
                    'prompt' => '--'.Yii::t('system','Select Active Type').'--'
            ]) ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdProblemType')->dropDownList( $model->getProblemTypes(), [
                    'prompt' => '--'.Yii::t('system','Select Problem Type').'--'
            ]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <?php if($admin): ?>
                <?= $form->field($model, 'IdEvaluatorUser')->widget( Select2::class,[]); ?>
            <?php else: ?>
                <?= Html::label($model->getAttributeLabel('IdEvaluatorUser'), $tableName.'-idevaluatoruser', []) ?>
                <?= Html::label(($model->IdEvaluatorUser ? $model->evaluatorUser->DisplayName : ''), null, [
                        'class' => 'form-control readonly'
                ]) ?>
                <?= $form->field($model, 'IdEvaluatorUser')->hiddenInput([])->label(false) ?>
            <?php endif; ?>
        </div>
        <div class="col-4">
            <?= $form->field($model, 'IdEvaluationValue')->textInput() ?>
        </div>
    </div>


    <?= $form->field($model, 'IdIncidentState')->textInput() ?>

    <?= $form->field($model, 'TicketProv')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CodEquipment')->textInput(['maxlength' => true]) ?>

<div class='row'>
    <div class="col-4">
        <?php if($admin):?>
            <?= $form->field($model, 'IdUser')->widget( Select2::class, [
                'readonly' => ( !$model->isNewRecord || $admin),
                'initValueText' => ( $model->IdUser ? $model->user->DisplayName : ''),
                'data'=> $model->getTechnicians(),
                'options' => ['placeholder' => '--Digite Usuario--'],
                'pluginOptions'=> [
                    'allowClear' => true,
                ],
                'pluginEvents'=> [
                    'change'=> "function(){ }",
                ],
            ]) ?>
        <?php  else: ?>
            <?= Html::label( ($model->getAttributeLabel('IdUser')), $tableName.'-iduser', [ 'class' => 'control-label']) ?>
            <?= Html::label( ($model->IdUser ? $model->user->DisplayName : ''), $tableName.'-iduser', [ 'class' => 'form-control disabled']) ?>
            <?= $form->field($model, 'IdUser')->hiddenInput()->label(false) ?>
        <?php endif; ?>
    </div>
    <div class="col-4">
        <?php if($model->isNewRecord || $admin ):?>
            <?= $form->field($model, 'IdAssignedUser')->widget( Select2::class, [
                'readonly' => ( !$model->isNewRecord || $admin),
                'initValueText' => ( $model->IdAssignedUser ? $model->assignedUser->DisplayName : ''),
                'data'=>$model->getTechnicians(),
                'options' => ['placeholder' => '--Digite Usuario--'],
                'pluginOptions'=> [
                    'allowClear' => true,
                ],
                'pluginEvents'=> [
                    'change'=> "function(){ }",
                ],
            ]) ?>
        <?php  else: ?>
            <?= Html::label( ($model->getAttributeLabel('IdAssignedUser')), $tableName.'-idassigneduser', [ 'class' => 'control-label']) ?>
            <?= Html::label( ($model->IdAssignedUser ? $model->assignedUser->DisplayName : ''), $tableName.'-idassigneduser', [ 'class' => 'form-control disabled']) ?>
            <?= $form->field($model, 'IdAssignedUser')->hiddenInput()->label(false) ?>
        <?php endif; ?>
    </div>
</div>
    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'Commentaries')->textarea(['rows' => 6]) ?>
        </div>
    </div>
    <?php  if($model->isNewRecord):?>
        <div class="row">
            <div class="col-12">
                <?= $form->field($model, 'fileattachment[]')->widget( FileInput::class, [
                    'options'=> [
                        'id'=> "$tableName-fileattachment",
                        'multiple'=> true,
                    ],
                    'pluginOptions' => [
                        'previewFileType' => 'any',
                        'dropZoneEnabled' => false,
                    ],
                ]) ?>
            </div>
        </div>
    <?php endif; ?>



    <?= $form->field($model, 'IdIncident')->hiddenInput()->label(false); ?>
    
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= Html::submitButton(
                        '<i class="fas fa-save"></i> '.Yii::t('app', 'Save'),
                        ['class' => 'btn btn-success']) ?>
                <?= Html::a(
                        '<i class="fas fa-times"></i> '.Yii::t('app', 'Cancel'),
                        ['incident/update','id' => $model->IdIncident ],
                        ['class' => 'btn btn-danger']) ?>
            </span>
        </div>
    </div>
</div>
<?= Html::hiddenInput('iddefaultcategory', $model->IdCategoryType, ['id' => $tableName.'-iddefaultcategory'])?>
<?= Html::hiddenInput('iddefaultsubcategory', $model->IdSubCategoryType, ['id' => $tableName.'-iddefaultsubcategory'])?>
<?php ActiveForm::end(); ?>
<?php
$script = <<< JS
    var defaultValues = $attributes
    
    $(document).ready(() => {
        $('#$tableName-idactivitytype').on('change', () => getAttributes());
        
        setAttributesForm( defaultValues )
                
        document.getElementById('$tableName-idcategorytype').addEventListener("change", () => {
            let _type = document.getElementById("$tableName-idcategorytype");
            let _option = _type.options[_type.selectedIndex].value
            fetchSubcategories( _option );
            fetchActivetypes( _option ) 
            
        });
        
        let enabled = new Event(changeCategoryState( !( parseInt('$enabled') === 1) ))
        document.getElementById('$tableName-enablereclasification').dispatchEvent(enabled)
        
        document.getElementById('$tableName-idactivetype').addEventListener("change", () => {
            let _type = document.getElementById("$tableName-idactivetype");
            let _option = _type.options[_type.selectedIndex].value
            fetchProblemtypes( _option , '$idProblemType');
        });
        
    });
    
    document.getElementById('$tableName-enablereclasification').addEventListener("change", () => {
        let enabled = document.getElementById("$tableName-enablereclasification");
        let _option = enabled.options[enabled.selectedIndex].value
        let changeState = !( parseInt(_option) === 1)
        
        changeState ? fetchSubcategories( parseInt('$idCategory') , '$idSubCategory' ) : null;
        changeState ? fetchActivetypes( parseInt('$idCategory'), '$idActiveType' ) : null
        changeState ? fetchProblemtypes( parseInt('$idActiveType'), '$idProblemType' ) : null
        
        changeCategoryState( changeState )
    });
    
    const changeCategoryState = ( state ) => {
        let type = document.getElementById("$tableName-idcategorytype");
        let subtype = document.getElementById("$tableName-idsubcategorytype");
        type.value = '$idCategory';
        subtype.value = '$idSubCategory';
        type.disabled = state
        subtype.disabled = state
    }
JS;
$this->registerJs($script);

$jsHead = <<< JS
    const setAttributesForm = ( data ) => {
        let attributes = Object.entries(data.results)
        $('.form-group').not(':hidden').hide();
        attributes.forEach( attribute => {
            let key = attribute[0]
            let value = attribute[1]
            let controlId = '.field-$tableName-'+(key.toLowerCase());
            let control = $(controlId);
            control.show()
            control.find('.form-group').show();
        })
        
    }
    
    const setAttributesFormAction = ( data ) => {
        const formChildren = document.querySelectorAll('#$formName input, #$formName select, #$formName textarea');
        formChildren.forEach( i => {
            if( i.name !== '$csrfParam'){
                let inputId = ( i.name.replace(']','').split('[') )
                //if( !(data.results.includes( inputId[1]) )){ //works with array
                let key = inputId[1]
                if( !( key in data.results )){ // works with object, aka: json
                    let tag = i.tagName.toLowerCase();
                    let input = $(i);
                    switch (tag){
                        case 'select':
                            //let option = input.find('option:first-child');
                            //input.val( option.val() )
                            i.selectedIndex = 0;
                            break;
                        default:
                            i.value = ''
                    }
                    input.trigger('change')                        
                }  
            }
        })
    }
    
    const setDefaultData = ( ) => {
        document.getElementById('$tableName-iddefaultcategory').value = '$idCategory'
        document.getElementById('$tableName-iddefaultsubcategory').value = '$idSubCategory'
        document.getElementById('$tableName-idcategorytype').value =  '$idCategory';
        document.getElementById('$tableName-idsubcategorytype').value =  '$idSubCategory';
    }
    
    const getAttributes = () => {
        var id = '$model->Id';
        var type = $('#$tableName-idactivitytype option:selected').val();
        
        AjaxHttpRequest( { 
            url: '$urlMainController/getattributelist',
            datatype: 'json',
            //search: {IdActivityType: type, Id: id },
            method: 'POST',
            formData: true,
            data: (new FormData( document.getElementById('$formName'))),
            success: data => setAttributesForm( data ),
            extra : data => setAttributesFormAction( data ),
            finally: data => setDefaultData()
        } )
    };
    
    const fetchSubcategories = ( idparent = null, selected = null) => {
        let url = new URL('$urlIncidentCategory/getlist') 
        let params= { idparent: idparent }
        //var select = $('#$tableName-idsubcategorytype');
        var select = document.getElementById('$tableName-idsubcategorytype')
        for( let i = 1; i < select.options.length; i++){
            select.options[i]= null;
        }
        url.search = new URLSearchParams( params ).toString();
        fetch( url )
            .then( response => response.json() ) 
            .then( data => {
                let disabled = select.disabled
                let selectedItem = 0;
                data.results.forEach( option => {
                    let opt = document.createElement('OPTION')
                    //let opt = $('<option></option>');
                    opt.value = option.id
                    opt.text = option.text
                    select.add( opt )
                    selectedItem = ( selected !== null && parseInt(selected) === parseInt( option.id )) ? (select.options.length - 1) : selectedItem 
                })
                select.selectedIndex = selectedItem
                select.disabled = disabled
            } )
     };        
    
    const fetchActivetypes  = (idparent = null, selected = null) => {
        let url = new URL('$urlActiveTypes/getlist') 
        let params= { idparent: idparent }
        var select = $('#$tableName-idactivetype');
        var firstOpt = select.find(':first-child');
        select.empty()
            .append( firstOpt );
        var selectProblem = $('#$tableName-idproblemtype');
        var firstOptProblem = selectProblem.find(':first-child');
        selectProblem.empty()
            .append( firstOptProblem );
        url.search = new URLSearchParams( params ).toString();
        fetch( url )
            .then( response => response.json() ) 
            .then( data => {
                data.results.forEach( option => {
                    let opt = $('<option></option>');
                    opt.val( option.id);
                    opt.html( option.text );
                    if( selected !== null && parseInt(selected) === parseInt( option.id )){
                        opt.attr('selected', true);
                    }
                    select.append( opt )
                })
            } )
    };
    
    const fetchProblemtypes = ( idparent = null, selected = null ) => {
        let url = new URL('$urlProblemTypes/getlist') 
        let params= { idparent: idparent }
        var select = $('#$tableName-idproblemtype');
        var firstOpt = select.find(':first-child');
        select.empty()
            .append( firstOpt );
        url.search = new URLSearchParams( params ).toString();
        fetch( url )
            .then( response => response.json() ) 
            .then( data => {
                data.results.forEach( option => {
                    let opt = $('<option></option>');
                    opt.val( option.id);
                    opt.html( option.text );
                    if( selected !== null && parseInt(selected) === parseInt( option.id )){
                        opt.attr('selected', true);
                    }
                    select.append( opt )
                })
            } )
    };
    
JS;
$this->registerJs($jsHead, yii\web\View::POS_END);

?>
