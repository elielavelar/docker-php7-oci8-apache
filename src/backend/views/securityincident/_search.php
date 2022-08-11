<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SecurityincidentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="securityincident-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'id' => $formName,
    ]); ?>
    <div class="row">
        <div class="col-2">
            <?= $form->field($model, 'Year')->dropDownList($model->getYearList(), ['prompt' => '--Seleccione Año--'])?>
        </div>
        <div class="col-10">
            <label>&nbsp;</label>
            <span class="float-right">
                <?= Html::submitButton('<i class="fas fa-search"></i> Filtrar', ['class' => 'btn btn-primary']) ?>
                <?= Html::button('<i class="fas fa-times"></i> Limpiar', ['type' => 'button','class' => 'btn btn-default','id' => 'btnReset']) ?>
            </span>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
