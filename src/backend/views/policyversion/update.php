<?php
use yii\helpers\Html;
use yii\bootstrap4\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\Policyversion */
/* @var $modelDetail \common\models\Attachment */

$this->title = Yii::t('app','{action} {entity}', ['action' => Yii::t('app', 'Update'), 'entity' => Yii::t('app', 'Version')]).': ' . $model->Version;
$this->params['breadcrumbs'][] = ['label' => Yii::t('system','Polic{n,plural,=0{ies} =1{y} other{ies}}', ['n' => 0]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Policyversion{n,plural,=0{s} =1{} other{s}}', ['n' => 0]), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Version, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = 'Actualizar';

$url = \Yii::$app->getUrlManager()->createUrl('policyversion/update/'.$model->Id);
$urlAttachment = \Yii::$app->getUrlManager()->createUrl('attachment');
?>
<div class="card">
    <div class="card-header bg-primary">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= Tabs::widget([
        'items' => [
            [
                'label' => 'General',
                'content' => $this->render('_form', ['model' => $model,'modelDetail' => $modelDetail]),
                'active' => TRUE
            ],
            [
                'label' => 'Aplicación de Versión',
                'content' => $this->render('_form/_detail',[
                    #'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail, 'dataProvider'=>$dataProvider,
                    ]),
                'active' => FALSE
            ],
        ]]);
 ?>
</div>
<?= $this->render('_form/_attachmentModal', ['model' => $modelDetail])?>
<?php
$titleDelete = Yii::t('app', 'Confirmation?');
$messageDelete = Yii::t('app', 'Are you sure you want to delete this record?');
$js = <<< JS
    $(document).ready(function (){
        $("#btn-LoadFile").on('click',function(){
            $("#modal-attachment").modal();
        });
        
        $('#btn-delete').on('click', function (){
            swal({
                title: '$titleDelete',
                text: '$messageDelete',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#00A65A",
                confirmButtonText: "Aceptar",
                closeOnConfirm: true
            }, function(){
                deleteAttachment($('#btn-delete').data('attachment'));
            });
        });
    });
JS;
$this->registerJs($js);

$script = <<< JS
    var refreshWindow = function(){
        window.location = "$url";
    };

    var deleteAttachment = function(id){
            var params = {};
            params.URL = "$urlAttachment/delete/"+id;
            params.DATA = {},
            params.METHOD = 'POST';
            params.DATATYPE = 'json';
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
                    refreshWindow();
                });
            };
            params.ERROR = function(data){
                swal("ERROR "+data.code, data.message, "error");
            };
            AjaxRequest(params);
        
    };
JS;
$this->registerJs($script, yii\web\View::POS_HEAD);
?>