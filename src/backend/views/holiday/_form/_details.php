<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\Holidays */
/* @var $modelDetails \common\models\Servicecentres */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="card-body">
    <div class="row">
        <?=$form->field($model, 'holidaysitems',['labelOptions'=>['style'=>'font-weight:bold; font-size: 24px']])->checkboxList($modelDetails, [
            'item'=> function ($index, $label, $name, $checked, $value){
                $checked = $checked == 1 ? "checked='checked'":"";
                return "<label class='checkbox col-3' style='font-weight: normal;'><input type='checkbox' {$checked} name='{$name}' value='{$value}'>{$label}</label>";
            },
        ]);?>
    </div>
</div>

