<?php

use kartik\form\ActiveForm;
use yii\helpers\StringHelper;
use backend\models\Typefields;
use backend\models\TokenType;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Typefields */
/* @var $form yii\widgets\ActiveForm */

$form = ActiveForm::begin([
    'id' => $formName
]);
?>
<div class="row">
    <div class="col-12">
        <?= $form->field($model, 'CustomLabel')->widget(Select2::className(),[
                        'data'=>$model->getFields(StringHelper::basename(TokenType::class)),
                        #'disabled'=> (!$model->isNewRecord),
                        'initValueText'=> ($model->CustomLabel ? $model->CustomLabel :""),
                        'options' => ['placeholder' => '--Seleccione Campo--'],
                        'size'=> Select2::SIZE_MEDIUM,
                        'pluginOptions'=> [
                            'allowClear' => true,
                        ],
                    'pluginEvents'=> [
                        'change'=> "function(){ }",
                    ],
            ]);
        ?>
    </div>
</div>
<?php
ActiveForm::end();
?>