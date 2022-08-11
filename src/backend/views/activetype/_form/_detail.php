<?php
use kartik\grid\GridView;
use kartik\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Activetype */
/* @var $modelDetail \backend\models\Problemtype */
/* @var $searchModel \backend\models\ProblemtypeSearch; */
/* @var $dataProvider \yii\data\ActiveDataProvider  */

$controller = 'problemtype';
$url = Yii::$app->getUrlManager()->createUrl($controller);

$create = Yii::$app->customFunctions->userCan($controller.'Create');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
$modalUploadName = $tableName.'-upload-modal';

$dtGrid = $tableName.'-grid';

$template = $update ? '{edit}':'';
$template .= $update ? '&nbsp;{update}':'';
$template .= $delete ? '&nbsp;|&nbsp;&nbsp;{delete}':'';
$csrfParam = Yii::$app->getRequest()->csrfParam;
?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <span class="float-left">
                    <?= $create ?
                        Html::button("<i class='fas fa-plus'></i> Agregar Problema", ['class'=>'btn btn-success','id'=>'btn-add'])
                        :"";
                    ?>
                </span>
                <span class="float-right">
                    <?= $create ?
                        $this->render('_modalUploadProblem', ['model' => $modelDetail])
                        :"";
                    ?>
                </span>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= GridView::widget([
                'id'=> $dtGrid,
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>TRUE,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'Name',
                    'Code',
                    [
                        'attribute'=>'IdComponentType',
                        'filter'=> $modelDetail->getComponentTypes(),
                        'content'=>function($data){
                            return $data->IdComponentType != 0 ? $data->componentType->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute'=>'IdState',
                        'filter'=> $modelDetail->getStates(),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    #'Description',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => $template,
                        'buttons'=>[
                            'edit' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-edit"></span>', "javascript:editProblem($model->Id);", [
                                            'title' => Yii::t('app', 'Actualizar Problema'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteProblem($model->Id);", [
                                            'title' => Yii::t('app', 'Eliminar Problema'), 
                                ]);
                            },
                            'update' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="fas fa-th-large"></span>', "javascript:updateProblem($model->Id)", [
                                    'title' => Yii::t('app', 'Detalle de Problema'),
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
<?=$this->render('_modalProblemType', ['model'=> $modelDetail])?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $("#btn-add").on('click',function(){
            $("#$modalName").modal();
        });

        $('#$modalName').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#btn-save-problem").on('click',function(){
            $("#$formName").submit();
        });
        
       $("#$formName").on('beforeSubmit',function(){
           AjaxHttpRequest({
                url: '$url/save',
                formData: true,
                data: new FormData( document.getElementById('$formName')),
                options: {
                    method: 'POST'
                },
                success: ( data ) => {
                    let response = {}
                    if( data.success ){
                        response = {
                            title: 'Registro Guardado!',
                            text: data.title,
                            icon: 'success',
                            button: 'Aceptar'
                        }
                        
                        $('#$modalName').modal('toggle');
                        refreshGrid();
                    } else {
                        response = {
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            button: 'Aceptar'
                        }
                        
                        setErrorsModel({
                            ID: '$formName',
                            PREFIX: '$tableName-',
                            ERRORS: data.errors
                        });
                    }
                    swal(response)
                }
           })
        }).on('submit', function(e){
            e.preventDefault();
        });
        
    });

    var clearModal = function(){
        let csrf = $('input[name=$csrfParam]').val();
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        $.extend(defaultvalues,{'$tableName-idactivetype':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
        $('input[name=$csrfParam]').val(csrf);
    };
JS;
$this->registerJs($js);


$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };

    var updateProblem = function(id){
        window.location = '$url/update/'+id;
    };
    
    var editProblem = function(id){
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
            frm.REPLACESTRING = {']':''};
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
        
    var deleteProblem = function(id){
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
$this->registerJs($script, yii\web\View::POS_HEAD);
?>
