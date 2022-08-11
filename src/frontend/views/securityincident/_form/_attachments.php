<?php

use yii\helpers\Html;

use backend\models\Securityincident;
use backend\models\Attachments;
use common\models\State;
use common\models\Type;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Securityincident */
/* @var $modelDetail backend\models\Attachments */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\AttachmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$filterState= ArrayHelper::map(State::findAll(['KeyWord'=> StringHelper::basename(Securityincident::class)]), 'Id', 'Name');
$filterType= ArrayHelper::map(Type::findAll(['KeyWord'=>StringHelper::basename(Securityincidentdetails::class)."Activity"]), 'Id', 'Name');
$urlAttachment = \Yii::$app->getUrlManager()->createUrl('attachment');

$tableName = $modelDetail->tableName();
$formName = $tableName.'-form';

$deleteAttachment = Yii::$app->customFunctions->userCan(StringHelper::basename(Securityincident::class).'DeleteAttachment') && $delete;

$templateDetail = '{download} ';
$templateDetail .= $deleteAttachment ? '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;| {delete}':'';

?>
<div class="box">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="row">
                <div class="col-lg-12">
                    <span class="pull-left">
                        <h4 class="panel-title">Detalle</h4>
                    </span>
                    <?php if($update):?>
                    <span class="pull-right">
                        <?= $this->render('_attachmentModal', ['model' => $modelDetail])?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="panel-body">
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
                        'template' => $templateDetail,
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