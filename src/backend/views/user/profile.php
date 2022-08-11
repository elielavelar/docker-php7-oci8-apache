<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\Tabs;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Perfil de Usuario: ' . $model->DisplayName;
$this->params['breadcrumbs'][] = 'Perfil';

$url = \Yii::$app->getUrlManager()->createUrl('user');
$tableName = 'user';
$formName = $tableName.'-form';
?>
<div class="user-update">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin([
            'id'=>$formName,
        ]); ?>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form/_userProfileForm', [
                            'model' => $model, 'form' => $form,
                        ]),
                        'active' => true
                    ],
                    [
                        'label' => 'Preferencias ',
                        'content' => $this->render('_form/_preferences',[
                            'model'=>$model #, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail
                            ]),
                        #'active' => true
                    ],
                ]]);
         ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php 
$script = <<< JS
    var getRandomPass = function(){
        var params = {};
        params.URL = "$url/getrandompass";
        params.DATA = {},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.PROCESSDATA = false;
        params.CONTENTTYPE = false;
        params.CACHE = false;
        params.SUCCESS = function(data){
            $("#$tableName-_password").val(data.password).keyup().blur();
            $("#$tableName-_passwordconfirm").val(data.password);
        };
        params.ERROR = function(data){
            swal("ERROR", data.message, "error");
        };
        AjaxRequest(params);
    };
        
    $(document).ready(function(){
        $('#btn-getrandompass').on('click',function(){
            getRandomPass();
        });
    });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>