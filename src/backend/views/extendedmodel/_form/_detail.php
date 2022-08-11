<?php

use kartik\grid\GridView;
use kartik\helpers\Html;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* @var $this yii\web\View */
/* @var $model \common\models\Extendedmodels */
/* @var $searchModel backend\models\ExtendedmodelkeySearch; */
/* @var $modelDetail common\models\Extendedmodelkeys */
/* @var $dataProvider yii\data\ActiveDataProvider  */

$tableName = $modelDetail->tableName();
$formName = 'form-' . $tableName;
$modalName = 'modal-' . $tableName;
$prefix = $tableName . '-';
$dtGrid = $tableName . '-grid';

$controller = Yii::$app->controller->id;
$template = "";
$template .= Yii::$app->customFunctions->userCan($controller . 'View') ? "{view} " : "";
$template .= Yii::$app->customFunctions->userCan($controller . 'Update') ? "{edit} {update} " : "";
$template .= Yii::$app->customFunctions->userCan($controller . 'Delete') ? " |&nbsp;&nbsp;&nbsp;&nbsp;{delete} " : "";

$filterAttributes = $model->getModelAttributes();
$filterState = $modelDetail->getStates();
$url = Yii::$app->getUrlManager()->createUrl($controller . 'key');
$urlModel = Yii::$app->getUrlManager()->createUrl('registredmodel');
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?=
                    Yii::$app->customFunctions->userCan($controller . 'Update') ?
                            #Html::button("<i class='fas fa-plus'></i> Agregar Llave", ['class'=>'btn btn-success','id'=>'btnAddDetail'])
                            Html::a('<i class="fas fa-plus"></i> Agregar Llave', [$controller . 'key/create', 'id' => $modelDetail->IdExtendedModel], ['class' => 'btn btn-success']) : "";
                    ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?=
                GridView::widget([
                    'id' => $dtGrid,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pjax' => true,
                    'columns' => [
                        ['class' => \kartik\grid\SerialColumn::class,],
                        [
                            'attribute' => 'AttributeKeyName',
                            'filter' => $filterAttributes,
                            'content' => function($model) {
                                return (($model->AttributeKeyName && $model->IdExtendedModel ) ? $model->extendedModel->getModelAttributeLabel($model->AttributeKeyName) : "");
                            },
                        ],
                        'AttributeKeyValue',
                        [
                            'attribute' => 'EnabledModelSource',
                            'content' => function($model) {
                                return $model->EnabledModelSource == $model::MODEL_SOURCE_ENABLED ? 'Sí' : 'No';
                            },
                        ],
                        [
                            'attribute' => 'IdState',
                            'filter' => Html::activeDropDownList($searchModel, 'IdState', $filterState, ['class' => 'form-control', 'prompt' => '--']),
                            'content' => function($data) {
                                return $data->IdState != 0 ? $data->state->Name : NULL;
                            },
                            'enableSorting' => TRUE
                        ],
                        'Description',
                        [
                            'class' => kartik\grid\ActionColumn::class,
                            'template' => $template,
                            'buttons' => [
                                'edit' => function ($url, $model) {
                                    #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-pencil-alt"></span>', "javascript:editDetail($model->Id);", [
                                                'title' => Yii::t('app', 'Actualizar Version'),
                                    ]);
                                },
                                'update' => function ($url, $model) {
                                    #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-th-large"></span>', "javascript:updateDetail($model->Id)", [
                                                'title' => Yii::t('app', 'Detalle de Version'),
                                    ]);
                                },
                                'delete' => function ($url, $model) {
                                    #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                    return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                                'title' => Yii::t('app', 'Eliminar Version'),
                                    ]);
                                },
                            ],
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
</div>
<?= $this->render('_modalDetail', ['model' => $modelDetail, 'modalName' => $modalName, 'formName' => $formName, 'parentModel' => $model]) ?>
<?php
$js = <<< JS
   $(document).ready(function(){
        
        $("#btn-add-condition").on('click', function(){
            addmodelattributes();
        });
        
        $("#btnAddDetail").on('click',function(){
            $("#$modalName").modal();
        });
        
        $("#btn-cancel-alt").on('click', function(){
            $("#$modalName").modal("toggle");
        });
        
        $("#btn-save-alt").on('click', function(){
            $("#$formName").submit();
        });

        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $('.btn-remove-cond').on('click', function(){
            
        });
        
        $("#$tableName-enabledmodelsource").on('change', function(){
            adminsource();
        });
        
        $("#$formName").on('beforeSubmit',function(){
            var data = new FormData(document.getElementById('$formName'));
            var params = {};
            params.URL = '$url/save';
            params.DATA = data;
            params.DATATYPE = 'json';
            params.METHOD = 'POST';
            params.CACHE = false;
            params.PROCESSDATA = false;
            params.CONTENTTYPE = false;
            params.SUCCESS = function(data){
                swal({
                    title: data.title,
                    text: data.message,
                    type: "success",
                    showCancelButton: false,
                    confirmButtonColor: "#00A65A",
                    confirmButtonText: "Aceptar",
                    closeOnConfirm: true
                }, function(){
                    refreshGrid();
                    $("#$modalName").modal("toggle");
                });
            };
            params.ERROR = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$formName";
                    errors.PREFIX = "$prefix";
                    errors.ERRORS = data.errors;
                    errors.EXTRA = function(){};
                    setErrorsModel(errors);
                }
            };
            AjaxRequest(params);
        }).on('submit', function(e){
            e.preventDefault();
        });
        
    });

    var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idextendedmodel':$model->Id});
        frm.DEFAULTS = defaultvalues;
        frm.EXTRA = function(){
            $('#$tableName-idregistredmodelsource').trigger('change');
            $("#$tableName-attributekeyname").trigger('change');
            $("#$tableName-attributekeyvalue").parents('div.value').show();
            $("#$tableName-value").parents('div.value').hide();
            $('#$tableName-enabledmodelsource').prop('checked',false).trigger('change');
            $('.conditions').find('.condition').remove();
        };
        clearForm(frm);
    };
JS;
$this->registerJs($js);


$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var updateDetail = function(id){
        window.location = '$url/update/'+id;
    };
        
    var editDetail = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            $('#tempvalue').val(data.AttributeSourceName);
            var frm = {};
            frm.ID = "$formName";
            frm.PREFIX = "$tableName-";
            frm.UPPERCASE = false;
            frm.SETBYID = false;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.EXCLUDE = ['tempvalue','value'];
            frm.SEPARATORS = ["["];
            frm.DATA = data;
            frm.INPUTEXTRAS = {};
            frm.EXECUTETRIGGER = true;
            frm.INPUTEXTRAS['AttributeKeyValue'] = function(){
            };
            frm.EXTRA = function(){
                var opt = new Option(data.AttributeKeyValue, data.AttributeKeyValue, true, true);
                $('#$tableName-value').append(opt).trigger('change');
            };
            setValuesForm(frm);
            var dvParent = $('div.conditions').find('div.card-body');
            $.each(data.conditions, function(i, field){
                var row = $("<div></div>");
                row.addClass('row').addClass('condition-row');
                row.append(field);
                dvParent.append(row);
            });
            $("#$modalName").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var deleteDetail = function(id){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa Eliminar este Registro?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: true
        },
        function(){
            var params = {};
            var data = {'Id':id};
            params.URL = "$url/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal(data.title, data.message, "warning");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
    
    var adminsource = function(){
        var enabled = $('#$tableName-enabledmodelsource option:selected').val() === '1';
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
        var selectedvalue = $("#tempvalue").val();
        var params = {};
        var data = {Id: id};
        params.URL = "$urlModel/getmodelattributes";
        params.DATA = data;
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            delete data['success'];
            $.each(data, function(i, value){
                selected = (i === selectedvalue);
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
            $("#$tableName-value").val('').trigger('change');
            $("#$tableName-AttributeKeyValue").val('').trigger('change');
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
$this->registerJs($script, yii\web\View::POS_HEAD);
?>
