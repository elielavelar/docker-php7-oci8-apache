<?php
use yii\helpers\Html;

use backend\models\TokenType;
use common\models\State;
use common\models\Type;
use backend\models\Typefields;
use common\models\Fields;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;


/* @var $this yii\web\View */
/* @var $model \backend\models\TokenType */
/* @var $modelDetail backend\models\Typefields */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\TypefieldSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= ArrayHelper::map(State::findAll(['KeyWord'=>StringHelper::basename(Typefields::class)]), 'Id', 'Name');
$filterField = ArrayHelper::map(Fields::findAll(['KeyWord'=> StringHelper::basename(TokenType::class)]), 'Id', 'Name');

$urlDetail = \Yii::$app->getUrlManager()->createUrl('typefield');
$formNameDetail = Typefields::tableName()."-form";
?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-12">
                    <span class="float-left">
                        <h4 class="card-title">Detalle</h4>
                    </span>

                    <span class="float-right">
                        <button type="button" id="btnAddDetail" name="btnAddDetail" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Agregar
                        </button>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?php Pjax::begin([
                    'id'=>'details',
                ]); ?>    
                <?= GridView::widget([
                'id'=>'dtgrid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    #'Id',
                    'Sort',
                    [
                        'attribute' => 'CustomLabel',
                        'content' => function($model){
                            return !empty($model->CustomLabel) ?  $model->CustomLabel : ( $model->IdField ? $model->field->Name : '');
                        },
                    ],
                    [
                        'attribute'=>'IdField',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdField', $filterField, ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdField != 0 ? $data->field->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    [
                        'attribute' => 'Required',
                        'filter' => [Typefields::REQUIRE_VALUE_FALSE => 'No', Typefields::REQUIRE_VALUE_TRUE => 'Sí'],
                        'content' => function($model){
                            return $model->Require == Typefields::REQUIRE_VALUE_TRUE ? 'Sí': 'No';
                        },
                    ],
                    [
                        'attribute'=>'IdState',
                        'filter'=> Html::activeDropDownList($searchModel, 'IdState', $filterState, ['class'=>'form-control','prompt'=>'--']),
                        'content'=>function($data){
                            return $data->IdState != 0 ? $data->state->Name:NULL;
                        },
                        'enableSorting'=>TRUE  
                    ],
                    
                    // 'Description:ntext',

                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{edit} {delete}',
//                        'buttons'=>[
//                            'edit' => function ($url, $model) {
//                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
//                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', "javascript:getDetail($model->Id);", [
//                                            'title' => Yii::t('app', 'lead-edit'), 
//                                ]);
//                            },
//                            'delete' => function ($url, $model) {
//                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
//                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', "javascript:deleteDetail($model->Id);", [
//                                            'title' => Yii::t('app', 'lead-delete'), 
//                                ]);
//                            },
//                        ],
                    ],
                ],
            ]); ?>
        <?php Pjax::end(); ?></div>
        </div>
    </div>
</div>
<?=$this->render('_modalMain', ['model'=> $modelDetail, 'formName' => $formNameDetail])?>
<?php
$script = <<< JS
    $(document).ready(function(){
        $("#btnAddDetail").on('click',function(){
            $("#modal-detail").modal();
        });
        
        $('#modal-detail').on('hidden.bs.modal', function () {
            clearModal();
        });
        
        $("#btnCancelDetail").on('click',function(){
            $("#modal-detail").modal("toggle");
        });
        
        $("#btnSaveDetail").on('click',function(){
            $("#settingsdetail-form").submit();
        });
    
        
        $("#settingsdetail-form").on('beforeSubmit',function(){
            $.ajax({
                url: "$urlDetail/save",
                type: "POST",
                data:  new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data)
                {		
                    var data = JSON.parse(data);
                    if(data.success == true)
                    {
                        $("#modal-detail").modal("toggle");
                        refreshGrid();
                    }
                    else
                    {
                        swal({html:true,title:"Error: "+data.code, text: data.message, type:"error"});
                        if(data.errors){
                            var errors = {};
                            errors.ID = "settingsdetail-form";
                            errors.PREFIX = "settingsdetail-";
                            errors.ERRORS = data.errors;
                            setErrorsModel(errors);
                        }
                    }
                },
                error: function() 
                {
                    console.log(data);
                } 	        
           });
        }).on('submit', function(e){
            e.preventDefault();
        });
    });
        
    var clearModal = function(){
        var frm = {};
        frm.ID = "settingsdetail-form";
        var defaultvalues = {};
        $.extend(defaultvalues,{'settingsdetail-idsetting':$model->Id});
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
        
JS;
$this->registerJs($script);

$script = <<< JS
    var refreshGrid = function(){
        $.pjax.reload({container:'#details',async: false});
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
            params.URL = "$urlDetail/delete/"+id;
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
        
    var getDetail = function(id){
        var params = {};
        var data = {'id':id};
        params.URL = "$urlDetail/get/";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var frm = {};
            frm.ID = "settingsdetail-form";
            frm.PREFIX = "settingsdetail-";
            frm.UNBOUNDNAME = true;
            frm.MATCHBYNAME = true;
            frm.SEPARATORS = ["[","]"];
            frm.DATA = data;
            setValuesForm(frm);
            $("#modal-detail").modal();
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
JS;
$this->registerJs($script, $this::POS_HEAD);

?>