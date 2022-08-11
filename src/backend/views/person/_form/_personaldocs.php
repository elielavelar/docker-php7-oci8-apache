<?php
use kartik\grid\GridView;
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Person */
/* @var $searchModel \common\models\search\PersonaldocumentSearch */
/* @var $modelDetail \common\models\Personaldocument */
/* @var $dataProvider yii\data\ActiveDataProvider  */

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';

$controller = 'personaldocument';
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$template = "{update}&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;{delete}";

$url = Yii::$app->getUrlManager()->createUrl($controller);
$dtGrid = $tableName.'-grid';
?>

<div class="row">
    <div class="col-12">
        <?= GridView::widget([
        'id'=>$dtGrid,
        'dataProvider' => $dataProvider,
        'pjax'=> true,
        'columns' => [
            ['class' => \kartik\grid\SerialColumn::class],
            [
                'attribute'=>'IdDocumentType',
                'content'=>function($model){
                    return $model->IdDocumentType ? $model->documentType->Name:NULL;
                },
                'enableSorting'=>true  
            ],
            'DocumentNumber',
            [
                'class' => \kartik\grid\ActionColumn::class,
                'template' => $template,
                'buttons'=>[
                    'update' => function ($url, $model) {
                        return Html::a('<span class="fas fa-edit"></span>', "javascript:editDetail($model->Id);", [
                                    'title' => Yii::t('app', 'Actualizar Detalle'), 
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteDetail($model->Id);", [
                                    'title' => Yii::t('app', 'Eliminar Detalle'), 
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
    </div>
</div>
<?php 
$js = <<< JS
   $(document).ready(function(){
        $('#btn-add').on('click', function(){
            $('#$modalName').modal();
        });
        
        $('#btn-save-alt').on('click', function(){
            $('#$formName').submit();
        });
        
        $('#btn-cancel-alt').on('click', function(){
            $('#$modalName').modal('toggle');
        });
        
        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
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
            params.SUCCESS = function(){
                swal(data.title, data.message, "success");
                $("#$modalName").modal("toggle");
                refreshGrid();
            };
            params.ERROR = function(data){
                swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                if(data.errors){
                    var errors = {};
                    errors.ID = "$formName";
                    errors.PREFIX = "$tableName-";
                    errors.ERRORS = data.errors;
                    errors.EXTRA = function(){
                        
                    };
                    setErrorsModel(errors);
                }
            };
            AjaxRequest(params);
        }).on('submit', function(e){
            e.preventDefault();
        });
   });
JS;
$this->registerJs($js);
$jsHead = <<< JS
    var refreshGrid = function(){
        //$.pjax.reload({container:'#$dtGrid-pjax'});
        location.reload(); 
    };    
    
    var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idperson':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
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
            frm.SETBYID = true;
            frm.REPLACESTRING = {'[]':'',']':''};
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["["];
            frm.DATA = data;
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
JS;
$this->registerJs($jsHead, $this::POS_HEAD);
?>