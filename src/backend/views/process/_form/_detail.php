<?php

use yii\helpers\Html;

use backend\models\Process;
use backend\models\Processdetail;
use common\models\Servicecentres;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Process */
/* @var $modelDetail backend\models\Processdetail */
/* @var $form yii\widgets\ActiveForm */
/* @var $searchDetail backend\models\ProcessdetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$urlDetail = \Yii::$app->getUrlManager()->createUrl('processdetail');

$controller = Yii::$app->controller->id;
$create = Yii::$app->customFunctions->userCan($controller.'Create');
$view = Yii::$app->customFunctions->userCan($controller.'View');
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');

$templateDetail = "";
$templateDetail .= $view ? ' {view} ':'';
$templateDetail .= $update ? ' {update} ':'';
$templateDetail .= $delete ? ' |&nbsp;&nbsp;&nbsp;{delete} ':'';

$centres = Servicecentres::find()
        ->joinWith('state b')
        ->where([
            
        ])->all();
$filterServicecentre = ArrayHelper::map($centres, 'Id','Name');

?>
<div class="box">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <?=$form->field($model, 'processitems',['labelOptions'=>['style'=>'font-weight:bold; font-size: 24px']])->checkboxList($modelDetail, [
                        'item'=> function ($index, $label, $name, $checked, $value){
                            $checked = $checked == 1 ? "checked='checked'":"";
                            return "<label class='checkbox col-3' style='font-weight: normal;'><input type='checkbox' {$checked} name='{$name}' value='{$value}'>{$label}</label>";
                        },
                    ]);?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
   $(document).ready(function(){
   });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>