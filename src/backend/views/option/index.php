
<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model \backend\models\Option */
/* @var $modelGroup \backend\models\Option */
/* @var $modelController backend\models\Option */
/* @var $modelAction backend\models\Option */
/* @var $modelPermission backend\models\Option */
/* @var $searchModel backend\models\OptionSearch */
/* @var $dataProvider \yii\data\ArrayDataProvider */

$this->title = Yii::t('app', 'System Options');
$this->params['breadcrumbs'][] = $this->title;

$url =  \Yii::$app->getUrlManager()->createUrl('option');
$tableName = $model->tableName();
$dtGrid = $tableName.'-grid';

$formNameModule = $tableName.'-module-form';
$modalNameModule = $tableName.'-module-modal';

$formNameGroup = $tableName.'-group-form';
$modalNameGroup = $tableName.'-group-modal';

$formNameController = $tableName.'-controller-form';
$modalNameController = $tableName.'-controller-modal';

$formNameAction = $tableName.'-action-form';
$modalNameAction = $tableName.'-action-modal';

$formNamePermission = $tableName.'-permission-form';
$modalNamePermission = $tableName.'-permission-modal';

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$view = Yii::$app->customFunctions->userCan($controller.'View');

$template = $view ? '{view}&nbsp;&nbsp;' : '';
$template .= $update ? '{update}&nbsp;&nbsp;': '';
$template .= $delete ? '|&nbsp;&nbsp;{delete}': '';

?>

<div class="options-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?=  Html::button('<i class="fas fa-plus-circle"></i> '.Yii::t('app', 'Add').' '. Yii::t('system','Module'), ['class'=>'btn btn-success','id'=>'btn-add-module']);?>
    </p>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?=GridView::widget([
                        'id' => $dtGrid,
                        'pjax' => true,
                        'dataProvider' => $dataProvider,
                        'hover' => true,
                        'columns' => [
                            [
                                'attribute' => 'Module',
                                'content' => function($model){
                                    $buttons = Html::a('<i class="fas fa-plus"></i>',"javascript:addGroup($model[IdModule]);",['class' => 'btn btn-success add-group'])
                                        .Html::a('<i class="fas fa-edit"></i>',"javascript:editModule($model[IdModule]);",['class' => 'btn btn-info upd-module'])
                                        .Html::a('<i class="fas fa-trash"></i>',"javascript:deleteModule($model[IdModule]);",['class' => 'btn btn-danger del-module']);;
                                    $buttonGroup = Html::tag('div', $buttons, ['class' => 'btn-group']);
                                    return Html::tag('i', null, ['class' => $model['IconModule']])
                                        .'&nbsp;&nbsp;<b>'.$model['Module'].'</b>'
                                        .Html::tag('span', $buttonGroup, ['class' => 'float-right']);
                                },
                                'group' => true,
                                'groupedRow' => true,
                                'groupOddCssClass' => 'kv-grouped-row',
                                'groupEvenCssClass' => 'kv-grouped-row',
                            ],
                            [
                                'attribute' => 'Group',
                                'content' => function($model){
                                    $buttons = Html::a('<i class="fas fa-plus"></i>',"javascript:addController($model[IdGroup]);",['class' => 'btn btn-success add-controller'])
                                        .Html::a('<i class="fas fa-edit"></i>',"javascript:editGroup($model[IdGroup]);",['class' => 'btn btn-info upd-group'])
                                        .Html::a('<i class="fas fa-trash"></i>',"javascript:deleteGroup($model[IdGroup]);",['class' => 'btn btn-danger del-group']);;
                                    $buttonGroup = Html::tag('div', $buttons, ['class' => 'btn-group']);
                                    return Html::tag('i', null, ['class' => $model['IconGroup']])
                                        .'&nbsp;&nbsp;<b>'.$model['Group'].'</b>'.Html::tag('span', $buttonGroup, ['class' => 'float-right']);
                                },
                                'contentOptions' => [
                                    'style' =>  [ 'margin-left' => '55px']
                                ],
                                'groupedRow' => true,
                                'group' => true,
                                'groupOddCssClass' => 'bg-info',
                                'groupEvenCssClass' => 'bg-info',
                            ],
                            [
                                'attribute' => 'Controller',
                                'content' => function($model){
                                    $buttons = Html::a('<i class="fas fa-plus"></i>',"javascript:addAction($model[IdController]);",['class' => 'btn btn-success add-action'])
                                        .Html::a('<i class="fas fa-edit"></i>',"javascript:editController($model[IdController]);",['class' => 'btn btn-info upd-controller'])
                                        .Html::a('<i class="fas fa-trash"></i>',"javascript:deleteController($model[IdController]);",['class' => 'btn btn-danger del-controller']);;
                                    $buttonGroup = Html::tag('div', $buttons, ['class' => 'btn-group']);
                                    return Html::tag('i', null, ['class' => $model['IconController']])
                                        .'&nbsp;&nbsp;<b>'.$model['Controller'].'</b>'.Html::tag('span', $buttonGroup, ['class' => 'float-right']);
                                },
                                'group' => true,
                                'groupOddCssClass' => 'bg-gradient-primary',
                                'groupEvenCssClass' => 'bg-gradient-teal',
                            ],
                            [
                                'attribute' => 'Name',
                                'content' => function($model){
                                    return Yii::t('app', \yii\helpers\ArrayHelper::getValue($model,'Name'));
                                }
                            ],
                            'KeyWord',
                            'Url',
                            'State',
                            [
                                'class' => \kartik\grid\ActionColumn::class,
                                'template' => $template,
                                'buttons' => [
                                        'view' => function( $url, array $model){
                                            $id = ArrayHelper::getValue($model, 'Id', false);
                                            return !$id
                                                ? ''
                                                : Html::a('<i class="fas fa-eye"></i>'
                                                , "javascript:editAction('$id')"
                                                , []
                                            );
                                        },'update' => function( $url, $model){
                                            $id = ArrayHelper::getValue($model, 'Id', false);
                                            return !$id
                                                ? ''
                                                : Html::a('<i class="fas fa-edit"></i>'
                                                , "javascript:editAction('$id')"
                                                , []
                                            );
                                        },
                                        'delete' => function( $url, $model){
                                            $id = ArrayHelper::getValue($model, 'Id', false);
                                            return !$id
                                                ? ''
                                                : Html::a('<i class="fas fa-trash"></i>'
                                                , "javascript:deleteAction('$id')"
                                                , [
                                                    //'data' => [
                                                    //    'confirm' => 'Está seguro que desea Eliminar este Registro?',
                                                    //    'method' => 'get',
                                                    //],
                                                ]
                                            );
                                    },
                                ]
                            ]
                        ],
                    ])?>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->render('_form/_modalModule', ['model'=>$model])?>
<?=$this->render('_form/_modalGroup', ['model'=> $modelGroup])?>
<?=$this->render('_form/_modalController', ['model'=>$modelController])?>
<?=$this->render('_form/_modalAction', ['model'=>$modelAction])?>
<?=$this->render('_form/_modalPermission', ['model' => $modelPermission])?>

<?php
$script = <<< JS
    $(document).ready(function(){
        /*MODULE*/
        $("#btn-add-module").on('click',function(){
            $("#$modalNameModule").modal();
        });
        
        $('#$modalNameModule').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#btn-cancel-module").on('click',function(){
            $("#$modalNameModule").modal("toggle");
        });
        
        $("#btn-save-module").on('click',function(){
            $("#$formNameModule").submit();
        });
    
        $("#$formNameModule").on('beforeSubmit',function(){
            var form = {};
            form.ID = '$formNameModule';
            form.PREFIX = '$tableName-module-';
            form.MODAL = '$modalNameModule';
            saveDataForm(form);
        }).on('submit', function(e){
            e.preventDefault();
        });
        
        /*GROUP*/
        
        $('#$modalNameGroup').on('hidden.bs.modal', function () {
            clearModalGroup();
        });
        
        $("#btn-cancel-group").on('click',function(){
            $("#$modalNameGroup").modal("toggle");
        });
        
        $("#btn-save-group").on('click',function(){
            $("#$formNameGroup").submit();
        });
    
        
        $("#$formNameGroup").on('beforeSubmit',function(){
            var form = {};
            form.ID = '$formNameGroup';
            form.PREFIX = '$tableName-group-';
            form.MODAL = '$modalNameGroup';
            saveDataForm(form);
        }).on('submit', function(e){
            e.preventDefault();
        });
        
        /*CONTROLLER*/
        
        $('#$modalNameController').on('hidden.bs.modal', function () {
            clearModalController();
        });
        
        $("#btn-cancel-controller").on('click',function(){
            $("#$modalNameController").modal("toggle");
        });
        
        $("#btn-save-controller").on('click',function(){
            $("#$formNameController").submit();
        });
    
        
        $("#$formNameController").on('beforeSubmit',function(){
            var form = {};
            form.ID = '$formNameController';
            form.PREFIX = '$tableName-controller-';
            form.MODAL = '$modalNameController';
            saveDataForm(form);
        }).on('submit', function(e){
            e.preventDefault();
        });
        
        /*ACTION*/
        
        $('#$modalNameAction').on('hidden.bs.modal', function () {
            clearModalAction();
        });
        
        $("#btn-cancel-action").on('click',function(){
            $("#$modalNameAction").modal("toggle");
        });
        
        $("#btn-save-action").on('click',function(){
            $("#$formNameAction").submit();
        });
    
        
        $("#$formNameAction").on('beforeSubmit',function(){
            var form = {};
            form.ID = '$formNameAction';
            form.PREFIX = '$tableName-action-';
            form.MODAL = '$modalNameAction';
            saveDataForm(form);
        }).on('submit', function(e){
            e.preventDefault();
        });
        
        /*PERMISSION*/
        
        $('#$modalNamePermission').on('hidden.bs.modal', function () {
            clearModalPermission();
        });
        
        $("#btn-cancel-permission").on('click',function(){
            $("#$modalNamePermission").modal("toggle");
        });
        
        $("#btn-save-permission").on('click',function(){
            $("#$formNamePermission").submit();
        });
    
        
        $("#$formNamePermission").on('beforeSubmit',function(){
            var form = {};
            form.ID = '$formNamePermission';
            form.PREFIX = '$tableName-permission-';
            form.MODAL = '$modalNamePermission';
            saveDataForm(form);
        }).on('submit', function(e){
            e.preventDefault();
        });
    });

    var clearModal = function(){
        var frm = {};
        frm.ID = "$formNameModule";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-module-idtype':$model->IdType,'$tableName-module-requireauth':$model->RequireAuth});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
    var clearModalGroup = function(){
        var frm = {};
        frm.ID = "$formNameGroup";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-group-idtype':$modelGroup->IdType,'$tableName-group-requireauth':$modelGroup->RequireAuth});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
    var clearModalController = function(){
        var frm = {};
        frm.ID = "$formNameController";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-controller-idtype':$modelController->IdType,'$tableName-controller-requireauth':$modelController->RequireAuth});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
    var clearModalAction = function(){
        var frm = {};
        frm.ID = "$formNameAction";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-action-idtype':$modelAction->IdType, '$tableName-action-itemmenu':$modelAction->ItemMenu,'$tableName-action-requireauth':$modelAction->RequireAuth,'$tableName-action-savelog':$modelAction->SaveLog,'$tableName-action-savetransaction':$modelAction->SaveTransaction});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
    var clearModalPermission = function(){
        var frm = {};
        frm.ID = "$formNamePermission";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-permission-idtype':$modelPermission->IdType, '$tableName-permission-itemmenu':$modelPermission->ItemMenu,'$tableName-permission-requireauth':$modelPermission->RequireAuth});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
JS;
$this->registerJs($script);

$head = <<< HJS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var saveDataForm = function(form){
        var data = new FormData(document.getElementById(form.ID));
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
                $("#"+form.MODAL).modal("toggle");
            });
        };
        params.ERROR = function(data){
            swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
            if(data.errors){
                var errors = {};
                errors.ID = form.ID;
                errors.PREFIX = form.PREFIX;
                errors.ERRORS = data.errors;
                errors.EXTRA = function(){};
                setErrorsModel(errors);
            }
        };
        AjaxRequest(params);
    };
        
    var addGroup = function(id){
        $("#$tableName-group-idparent").val(id);
        $("#$modalNameGroup").modal();
    };
        
    var addController = function(id){
        $("#$tableName-controller-idparent").val(id);
        $("#$modalNameController").modal();
    };
        
    var addAction = function(id){
        $("#$tableName-action-idparent").val(id);
        $("#$modalNameAction").modal();
    };
        
    var addPermission = function(id){
        $("#$tableName-permission-idparent").val(id);
        $("#$modalNamePermission").modal();
    };
            
   var deletePermission = function(id){
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
        
    var deleteModule = function(id){
        deletePermission(id);
    };
        
    var deleteGroup = function(id){
        deletePermission(id);
    };
        
    var deleteController = function(id){
        deletePermission(id);
    };
        
    var deleteAction = function(id){
        deletePermission(id);
    };
        
    var editModule = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formNameModule";
            frm.PREFIX = "$tableName-module-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#$modalNameModule").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var editGroup = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formNameGroup";
            frm.PREFIX = "$tableName-group-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#$modalNameGroup").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var editController = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formNameController";
            frm.PREFIX = "$tableName-controller-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#$modalNameController").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var editAction = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formNameAction";
            frm.PREFIX = "$tableName-action-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#$modalNameAction").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
        
    var editPermission = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formNamePermission";
            frm.PREFIX = "$tableName-permission-";
            frm.UPPERCASE = false;
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
            frm.DATA = data;
            setValuesForm(frm);
            $("#$modalNamePermission").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
HJS;
$this->registerJs($head, yii\web\View::POS_HEAD);
?>
