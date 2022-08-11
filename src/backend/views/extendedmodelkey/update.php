<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Extendedmodelkey */

$this->title = 'Actualizar Llave Modelo Extendido: ' . ($model->IdExtendedModel ? $model->extendedModel->registredModel->Name : '').': ['.$model->AttributeKeyName.(!empty($model->AttributeKeyValue) ? ':'.$model->AttributeKeyValue: '').']';
$this->params['breadcrumbs'][] = 'Configuraciones';
$this->params['breadcrumbs'][] = ['label' => 'Modelos Extendidos', 'url' => ['extendedmodel/index']];
$this->params['breadcrumbs'][] = ['label' => ($model->IdExtendedModel ? $model->extendedModel->registredModel->Name : ''), 'url' => ['extendedmodel/view' , 'id'=> $model->IdExtendedModel]];
$this->params['breadcrumbs'][] = ['label' => 'Llave', 'url' => ['extendedmodel/index']];
$this->params['breadcrumbs'][] = ['label' => $model->AttributeKeyName.':'.($model->AttributeKeyValue)];
$this->params['breadcrumbs'][] = 'Actualizar';

$controller = Yii::$app->controller->id;
$url = Yii::$app->getUrlManager()->createUrl($controller);
$urlModel = Yii::$app->getUrlManager()->createUrl('registredmodel');

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
        model['Id'] =  $('#$tableName-idregistredmodelsource option:selected').val();
        model['keyAttribute'] = $('#$tableName-attributesourcename option:selected').val();
        $.extend(data, model);
        return data;
    }
JS;

?>
<div class="extendedmodels-update">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= Tabs::widget([
            'items' => [
                [
                    'label' => 'General',
                    'content' => $this->render('_form', ['model' => $model]),
                    'active' => TRUE
                ],
                [
                    'label' => 'Grupos/Campos',
                    'content' => $this->render('_form/_detail',[
                        'model'=>$model, 
                        'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                        'modelField' => $modelField,
                        ]),
                    #'visible' => $model->IdState ? (in_array($model->state->Code, [Catalogs::STATUS_ACTIVE])):false,
                    'active' => FALSE
                ],
            ]]);
     ?>
    </div>
</div>
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
        params.URL = "$url/getmodelattributesform";
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