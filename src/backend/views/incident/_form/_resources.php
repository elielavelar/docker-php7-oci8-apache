<?php
use kartik\grid\GridView;
use common\customassets\Timeline\Html;

/* @var $modelDetail \backend\models\Incidentresource */
/* @var $searchModel \backend\models\search\IncidentresourceSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$url = \Yii::$app->getUrlManager()->createUrl('incidentresource');

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';
$modalName = $tableName.'-modal';
$dtGrid = $tableName.'-grid';
$controller = Yii::$app->controller->id;

$update = Yii::$app->customFunctions->userCan($controller.'Update');

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
                            <?=Html::a(
                                    Html::icon('fas fa-plus-circle').' '.Yii::t('app', 'Add'),
                                    'javascript:void(0);',
                                    [
                                                'id' => 'btn-add-resource', 'class' => 'btn btn-success'
                                            ])?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <?= GridView::widget([
                            'id'=> $dtGrid,
                            'pjax' => true,
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],
                                'IdResource',
                                'Description:ntext',
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'template' => '{download}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;| {delete}',
                                    'buttons'=>[
                                        'delete' => function ($url, $model) {
                                            #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                            return Html::a('<span class="fas fa-trash"></span>', "javascript:deleteResource($model->Id);", [
                                                'title' => Yii::t('app', 'lead-delete'),
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
    </div>
<?= $this->render('_resourceModal', ['model' => $modelDetail ])?>
<?php
$js = <<< JS
    $(document).ready(function(){
        $('#btn-add-resource').on('click', () => $('#$modalName').modal())
        
        $('#$modalName').on('hidden.bs.modal', function () {
            $("#fileattachment").fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh');
            $("#$tableName-description").val('');
        });
    });
        
    var clearModalResource = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
JS;
$this->registerJs($js);
$script = <<< JS
    var refreshGridResources = function(){
        $.pjax.reload({container:'#$dtGrid-pjax'});
    };
        
    var deleteResource = function(id){
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
                refreshGridAttachments();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
JS;
$this->registerJs($script, $this::POS_HEAD);

?>