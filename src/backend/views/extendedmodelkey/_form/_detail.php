<?php
use kartik\grid\GridView;
use kartik\helpers\Html;
use kartik\grid\ExpandRowColumn;

/* @var $this yii\web\View */
/* @var $model \common\models\Extendedmodelkey */
/* @var $searchModel backend\models\ExtendedmodelfieldgroupSearch */
/* @var $modelDetail common\models\Extendedmodelfieldgroup */
/* @var $dataProvider yii\data\ActiveDataProvider  */
/* @var $modelField common\models\Extendedmodelfield */

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
$prefix = $tableName.'-';
$dtGrid = $tableName.'-grid';

$tableNameField = $modelField->tableName();
$formNameField = $tableNameField.'-form';
$modalNameField = $tableNameField.'-modal';
$prefixField = $tableNameField.'-';
$dtGridField = $tableNameField.'-grid';

$controller = Yii::$app->controller->id;
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
$template = "";
$template .= $update ? "{edit} {update} ":"";
$template .= $delete ? " |&nbsp;&nbsp;&nbsp;&nbsp;{delete} ":"";

$url = Yii::$app->getUrlManager()->createUrl('extendedmodelfieldgroup');
$urlField = Yii::$app->getUrlManager()->createUrl('extendedmodelfield');

?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Yii::$app->customFunctions->userCan($controller.'Update') ? 
                        Html::button("<i class='fas fa-plus'></i> Agregar Grupo", ['class'=>'btn btn-success','id'=>'btn-add-detail'])
                        :"";
                    ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                'id'=>$dtGrid,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>true,
                'columns' => [
                    ['class' => \kartik\grid\SerialColumn::class],
                    [
                        'class' => ExpandRowColumn::class,
                        'width' => '50px',
                        # show row expanded for even numbered keys
                        #'detailUrl' => Url::to(['/site/book-details']),
                        'detail' => function($model, $key, $index, $column) {
                            return Yii::$app->controller->renderPartial('_form/_rowdetails', ['model' => $model]);
                        },
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_EXPANDED;
                        },
                        'headerOptions' => ['class' => 'kartik-sheet-style'],
                        'expandOneOnly' => true
                    ],
                    [
                        'attribute' => 'Sort',
                        'width' => '10%',
                    ],
                    'Name',
                    [
                        'attribute' => 'VisibleContainer',
                        'filter' => [$modelDetail::VISIBLE_CONTAINER_DISABLED => 'No', $modelDetail::VISIBLE_CONTAINER_ENABLED => 'Sí'],
                        'content' =>  function($model){
                            return $model->VisibleContainer == $model::VISIBLE_CONTAINER_ENABLED ? 'Sí' : 'No';
                        },
                    ],
                    [
                        'attribute' => 'IdState',
                        'filter' => $modelDetail->getStates(),
                        'content' => function($model){
                            return $model->IdState ? $model->state->Name : '';
                        },
                    ],
                    'Description',
                    [
                        'class' => kartik\grid\ActionColumn::class,
                        'template' => $template,
                        'buttons'=>[
                            'update' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-pencil"></span>', "javascript:editDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Actualizar Campo'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-trash-o"></span>', "javascript:deleteDetail($model->Id);", [
                                            'title' => Yii::t('app', 'Eliminar Campo'), 
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
            </div>
        </div>
    </div>
</div>
<?=$this->render('_modalDetail',['model' => $modelDetail]);?>
<?=$this->render('_modalField',['model' => $modelField]);?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $('#btn-add-detail').on('click', function(){
            $('#$modalName').modal();
        });
        
        $("#btn-cancel-alt").on('click', function(){
            $("#$modalName").modal('toggle');
        });
        
        $("#btn-save-alt").on('click', function(){
            $("#$formName").submit();
        });
        
        $("#$formName").on('beforeSubmit',function(){
            var data = new FormData(document.getElementById('$formName'));
            var chck = $('#$tableName-visiblecontainer')
            var state = chck.bootstrapSwitch('state');
            var val = state ? 1:0;
            data.set(chck.attr('name'),val);
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
                    errors.PREFIX = "$tableName-";
                    errors.ERRORS = data.errors;
                    errors.EXTRA = function(){};
                    setErrorsModel(errors);
                }
            };
            AjaxRequest(params);
        }).on('submit', function(e){
            e.preventDefault();
        });

        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#btn-cancel-fld").on('click', function(){
            $("#$modalNameField").modal('toggle');
        });
        
        $("#btn-save-fld").on('click', function(){
            $("#$formNameField").submit();
        });
        
        $("#$formNameField").on('beforeSubmit',function(){
            var data = new FormData(document.getElementById('$formNameField'));
            console.log(data);
            var params = {};
            params.URL = '$urlField/save';
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
                    $("#$modalNameField").modal("toggle");
                });
            };
            params.ERROR = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$formNameField";
                    errors.PREFIX = "$tableNameField-";
                    errors.ERRORS = data.errors;
                    errors.EXTRA = function(){};
                    setErrorsModel(errors);
                }
            };
            AjaxRequest(params);
        }).on('submit', function(e){
            e.preventDefault();
        });
        
        $('#$modalNameField').on('hidden.bs.modal', function () {
            clearModalField();
        });
        
        var clearModal = function(){
            var frm = {};
            frm.ID = "$formName";
            var defaultvalues = {};
            $.extend(defaultvalues,{'$tableName-sort':$modelDetail->Sort,'$tableName-idextendedmodelkey':$model->Id});
            frm.DEFAULTS = defaultvalues;
            frm.EXTRA = function(){
                $('#$tableName-visiblecontainer').trigger('change');
                $("input[type=hidden][name='Extendedmodelfieldgroup[VisibleContainer]']").val('0');
            }
            clearForm(frm);
        };
        
        var clearModalField = function(){
            var frm = {};
            frm.ID = "$formNameField";
            var defaultvalues = {};
            $.extend(defaultvalues,{'$tableNameField-sort':$modelField->Sort, '$tableNameField-colspan' :$modelField->ColSpan, '$tableNameField-rowspan':$modelField->RowSpan});
            frm.DEFAULTS = defaultvalues;
            frm.EXTRA = function(){
                $('#$tableNameField-idfield').trigger('change');
                $('#$tableNameField-required').trigger('change');
            };
            clearForm(frm);
        };
   });
JS;
$this->registerJS($js);

$jsHead = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var addField = function(id){
        $("#$tableNameField-idextendedmodelfieldgroup").val(id);
        $("#$modalNameField").modal();
    };
        
    var editDetail = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$url/get";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formName";
            frm.PREFIX = "$tableName-";
            frm.UPPERCASE = false;
            frm.SETBYID = false;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
            frm.DATA = data;
            frm.INPUTEXTRAS = {};
            frm.EXECUTETRIGGER = true;
            setValuesForm(frm);
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
            var data = {'id':id};
            params.URL = "$url/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal("Registro Eliminado", data.message, "warning");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
    var editField = function(id){
        var params = {};
        var data = {'Id':id};
        params.URL = "$urlField/get";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "$formNameField";
            frm.PREFIX = "$tableNameField-";
            frm.UPPERCASE = false;
            frm.SETBYID = false;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
            frm.DATA = data;
            frm.INPUTEXTRAS = {};
            frm.EXECUTETRIGGER = true;
            setValuesForm(frm);
            $("#$modalNameField").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
    
    var deleteField = function(id){
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
            var data = {'id':id};
            params.URL = "$urlField/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal("Registro Eliminado", data.message, "warning");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
JS;
$this->registerJs($jsHead, $this::POS_HEAD);
?>