<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Person */
/* @var $modelDetail common\models\Personaldocument */
/* @var $searchModel \common\models\PersonaldocumentSearch */
/* @var $dataProvider \yii\data\ActiveDataProvider */

$this->title = 'Agregar Persona';
$this->params['breadcrumbs'][] = ['label' => 'Personas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$url = Yii::$app->getUrlManager()->createUrl('person');
?>
<div class="person-create">
    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model,
            'modelDetail' => $modelDetail,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]) ?>
    </div>
</div>
<?php
$js = <<< JS
    $(document).ready(function(){
        $("#btn-add").on('click', function(){
            addfield();
        });
    });
JS;
$this->registerJs($js, $this::POS_READY);

$jsHead = <<< JS
    var addfield = function(){
        var params = {};
        var data = {};
        params.URL = "$url/getdocumentfield";
        params.DATA = {'data':JSON.stringify(data)},
        params.METHOD = 'POST';
        params.DATATYPE = 'json';
        params.SUCCESS = function(data){
            var div = $("div.div-docs").find('div.card-body');
            div.append(data.field);
        };
        params.ERROR = function(data){
            swal("ERROR "+data.code, data.message, "error");
        };
        AjaxRequest(params);
    };
    
    var removeField = function(e){
        var parent = $(e).parents('div.row-field');
        console.log(parent);
        parent.remove();
    };
JS;
$this->registerJs($jsHead, $this::POS_HEAD);
?>