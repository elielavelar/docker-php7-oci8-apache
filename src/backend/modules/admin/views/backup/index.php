<?php
use yii\bootstrap\Html;
use yii\web\View;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use kartik\form\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\BackupSchema */
/* @var $dataProvider yii\data\ArrayDataProvider */
$this->title = 'Respaldo de Base de Datos';
$this->params['breadcrumbs'][] = 'Respaldo';
$url =  \Yii::$app->getUrlManager()->createUrl('backup/backup');
$urlDefault =  \Yii::$app->getUrlManager()->createUrl('backup');
$urlDownload =  $url."/download";
$urlRestore =  $urlDefault."/restore";

$tableName = $model->getModel();

?>
<div class="backup-default-index">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3><?=$this->title;?></h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <p class="pull-right">
                        <?= $model->create ? Html::a('<i class="fa fa-file-archive-o"></i> Crear', ['create'], ['class' => 'btn btn-success']):"" ?>
                        <?= Html::button('<i class="fa fa-refresh"></i> Refrescar Lista',['id'=>'btnRefresh','class'=>'btn btn-primary'])?>
                        <?php Modal::begin([
                                'header'=>'<h4>Cargar Respaldo</h4>',
                                'headerOptions'=>[
                                    'class'=>'bg-blue',
                                ],
                                'toggleButton'=>[
                                    'label'=>'<i class="fa fa-upload"></i> Cargar Archivo','class'=>'btn btn-default',
                                ],
                            ]);
                            $form = ActiveForm::begin([
                                'options'=>[
                                    'enctype'=>'multipart/form-data',
                                ],
                            ]);
                            echo $form->field($model, 'uploadFile')->widget(FileInput::className(), [
                                'pluginOptions'=>[
                                    'uploadUrl'=> Url::to(['backup/upload']),
                                    'showPreview' => false,
                                ],
                            ]);

                            ActiveForm::end();
                            Modal::end();
                            ?>
                    </p>
                </div>
            </div>
            <?php Pjax::begin([
                'id'=>$tableName."-grid"
            ]);?>
            <?= GridView::widget([
                'id'=>$tableName.'-detail',
                'dataProvider'=>$dataProvider,
                'rowOptions'=>[
                    'enableRowClick'=>true,
                    'ExpandRowColumn'=>[],
                ],
                'columns'=>[
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute'=>'id',
                    ],
                    [
                        'attribute'=>'name',
                        'content'=>function($m){
                            return $m['download'] ? Html::a($m['name'], ['download', 'id' => $m['name']], [
                                                'title'=>'Descargar',
                                                    'data' => [
                                                        'method' => 'post',
                                                    ],
                                ]):$m["name"];
                        },
                    ],
                    'size',
                    'created_time',
                    'modified_time',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {delete} {download}',
                        'buttons'=>[
                            'view'=>function ($url, $m) {
                                return $m["view"] ? 
                                        Html::a("<span class='glyphicon glyphicon-eye-open'></span>", "javascript:viewFile('$m[name]')",[
                                                'title'=>'Ver Detalles',
                                                    'data' => [
                                                        'method' => 'post',
                                                    ],
                                ]):"";
                            },
                            'delete'=>function ($url, $m) {
                                return $m["delete"] ? 
                                        Html::a("<span class='glyphicon glyphicon-trash'></span>", "javascript:deleteBackup('$m[name]')",[
                                                'title'=>'Eliminar',
//                                                    'data' => [
//                                                        'confirm' => 'Está seguro que desea Eliminar este Respaldo?',
//                                                        'method' => 'post',
//                                                    ],
                                ]):"";
                            },
                            'download'=>function ($url, $m) {
                                return $m["download"] ? 
                                        Html::a("<span class='glyphicon glyphicon-download-alt'></span>", ['download', 'id' => $m['name']],[
                                                'title'=>'Descargar',
                                                    'data' => [
                                                        'method' => 'post',
                                                    ],
                                ]):"";
                                        /*"<a href='".$m["url"]."' target='_blank' title='Descargar'>"
                                        . "<span class='glyphicon glyphicon-download-alt'></span></a>":"";*/
                            },
                        ],
                    ],
                ],
            ]);
            Pjax::end();
            ?>
            
        </div>
    </div>
</div>
<div class="modal fade in" id="modal-detail" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h3 class="modal-title" id="Label"><strong>Detalle de Archivo</strong></h3>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-4">
                            <span class="bs-glyphicons-giant">
                                <i id="icon"></i>
                            </span>
                        </div>
                        <div class="col-md-8">
                            <div class="col-md-12">
                                <strong><?=$model->getAttributeLabel('name')?>: </strong><label class="" id="name"></label>
                            </div>
                            <div class="col-md-12">
                                <b><?=$model->getAttributeLabel('size')?>: </b><label class="" id="size"></label>
                            </div>
                            <div class="col-md-12">
                                <b><?=$model->getAttributeLabel('created_time')?>: </b><label class="" id="created_time"></label>
                            </div>
                            <div class="col-md-12">
                                <b><?=$model->getAttributeLabel('modified_time')?>: </b><label class="" id="modified_time"></label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="row">
                    <span class="pull-left">
                        <?= $model->download ? Html::a("<span class='glyphicon glyphicon-download-alt'></span> Descargar", '',[
                                                'class'=>'btn btn-primary',
                                                'title'=>'Descargar',
                                                'id'=>'href-download',
                                                    'data' => [
                                                        'method' => 'post',
                                                    ],
                                ]):""?>
                        <?= $model->restore ? Html::a("<span class='glyphicon glyphicon-repeat'></span> Restaurar", '',[
                                                'class'=>'btn btn-warning',
                                                'title'=>'Restaurar',
                                                'id'=>'href-restore',
                                                    'data' => [
                                                        'method' => 'post',
                                                    ],
                                ]):""?>
                    </span>
                    <span class="pull-right">
                        <?=Html::button('<i class="fa fa-times"></i> Cerrar', ['id'=>'btnCloseDetail','class'=>'btn btn-danger']);?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
   $(document).ready(function(){
        //refreshGrid();
        $("#btnRefresh").on('click',function(){
            refreshGrid();
        });
        
        $("#btnCloseDetail").on('click',function(){
            $("#modal-detail").modal("toggle");
        });
        
        $('#modal-detail').on('hidden.bs.modal', function () {
            clearModal();
        });
   });
JS;
$this->registerJs($script, View::POS_READY);

$script = <<< JS
    
    var viewFile = function(id){
        var data = {'name':id};
        var params = {};
        params.URL = "$url/view";
        params.DATA = {'data':JSON.stringify(data)};
        var success = function(data){
            console.log(data);
        };
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            $("#icon").addClass(data.icon);
            $("#name").html(data.name);
            $("#created_time").html(data.created_time);
            $("#modified_time").html(data.modified_time);
            $("#size").html(data.size);
            $("#href-download").attr('href','$urlDownload?id='+data.name);
            $("#href-restore").attr('href','javascript:restoreBackup("'+data.name+'")');
            $("#modal-detail").modal();
        };
        params.ERROR = function(data){
            console.log(data.message);
        };
        AjaxRequest(params);
        
    };
        
    var restoreBackup = function(id){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desea Restaurar este Respaldo?<br><strong>ADVERTENCIA: Esta operación no se puede ser Deshacer</strong>",
            type: "warning",
            html: true,
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Restaurar!",
            closeOnConfirm: true
        },
        function(){
            var params = {};
            var data = {'id':id};
            params.URL = "$url/restore?id="+id;
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
        
    var deleteBackup = function(id){
        swal({
            title: "Confirmación?",
            text: "¿Está seguro que desesa Eliminar este Respaldo?",
            type: "error",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Eliminar",
            closeOnConfirm: true
        },
        function(){
            var params = {};
            var data = {'id':id};
            params.URL = "$url/delete?id="+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
            params.SUCCESS = function(data){
                swal({
                    title: "Respaldo Eliminado",
                    text: data.message,
                    type: "warning",
                    showCancelButton: false,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "ACEPTAR",
                    closeOnConfirm: true,
                    closeOnCancel: false
                  },
                  function(isConfirm){
                    //location.reload();
                  });
                  refreshGrid();
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        });
    };
        
    var clearModal = function(){
        $("#icon").removeAttr('class');
        $("#modal-detail div.modal-body").find('label').html("");
    };
        
    var refreshGrid = function(){
        $.pjax.reload({container:'#$tableName-grid'});
    };
JS;
$this->registerJs($script, View::POS_HEAD);
?>