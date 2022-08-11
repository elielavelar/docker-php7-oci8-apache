<?php

use yii\helpers\Html;

use backend\models\Securityincidentdetails;
use backend\models\Attachments;
use common\models\State;
use common\models\Type;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincidentdetails */
/* @var $modelDetail backend\models\Attachments */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\AttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$urlAttachment = \Yii::$app->getUrlManager()->createUrl('attachment');

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';

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
                        <?= $this->render('_attachmentModal', ['model' => $modelDetail])?>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <?php Pjax::begin([
                    'id'=>'detailsattachments'
                ]); ?>    
                <?= GridView::widget([
                'id'=>'dtgrid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    #'KeyWord',
                    'FileName',
                    [
                        'attribute' => 'IdCatalogDetail'
                        ,'content' => function($model){
                            return $model->IdCatalogDetail ? $model->catalogDetail->Name:'';
                        }
                    ],
                     'Description:ntext',
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{download}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;| {delete}',
                        'buttons'=>[
                            'download' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['securityincidentdetail/update','id'=>$model->Id]);
                                return Html::a('<span class="glyphicon glyphicon-save"></span>', 'javascript: downloadAttachment("'.$model->path.'");', [
                                            'title' => Yii::t('app', 'lead-download'), 
                                ]);
                            },
                            'delete' => function ($url, $model) {
                                #$urlDetail = \Yii::$app->getUrlManager()->createUrl(['settingsdetail/delete','id'=>$model->Id]);
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', "javascript:deleteAttachment($model->Id);", [
                                            'title' => Yii::t('app', 'lead-delete'), 
                                ]);
                            },
                        ],
                          
                         
                    ],
                ],
            ]); ?>
        <?php Pjax::end(); ?></div>
        </div>
    </div>
</div>
<?php
$js = <<< JS
    $(document).ready(function(){
        $('#modal-attachment').on('hidden.bs.modal', function () {
            $("#fileattachment").fileinput('disable').fileinput('enable').fileinput('clear').fileinput('refresh');
            $("#$tableName-description").val('');
        });
    });
        
    var clearModal = function(){
        var frm = {};
        frm.ID = "$formName";
        var defaultvalues = {};
        frm.DEFAULTS = defaultvalues;
        clearForm(frm);
    };
JS;
$this->registerJs($js);

$script = <<< JS
   var refreshGridAttachments = function(){
        $.pjax.reload({container:'#detailsattachments',async: false});
    };
        
    var downloadAttachment = function(path){
        window.open( path );
    };
        
    var deleteAttachment = function(id){
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
            params.URL = "$urlAttachment/delete/"+id;
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