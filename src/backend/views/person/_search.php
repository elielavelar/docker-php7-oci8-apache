<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PersonSearch */
/* @var $modelDetail common\models\Personaldocument */
/* @var $form ActiveForm */
$formName = 'search-form';
$prefix = 'search';
?>
<div class="person-search">
    <?php $form = ActiveForm::begin([
        'id' => $formName,
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="card">
        <div class="card-header bg-info">
            <h4 class="card-title">Filtros</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-2">
                    <?=$form->field($model, 'Code')->textInput(); ?>
                </div>
                <div class="col-2">
                    <?= $form->field($model, 'FirstName')->textInput() ?>
                </div>
                <div class="col-2">
                    <?= $form->field($model, 'SecondName') ?>
                </div>
                <div class="col-2">
                    <?= $form->field($model, 'LastName') ?>
                </div>
                <div class="col-2">
                    <?= $form->field($model, 'SecondLastName') ?>
                </div>
                <div class="col-2">
                    <div class="btn-group" role="group" id="btn-grp">
                        <?= Html::submitButton('<i class="fas fa-search"></i> Filtrar', ['class' => 'btn btn-lg btn-primary','id' => 'btn-submit-search']) ?>
                        <?= Html::button('<i class="fas fa-undo"></i> Limpiar', ['class' => 'btn btn-lg btn-default','id' => 'btn-reset-search']) ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <?=$form->field($model, 'IdDocumentType')->dropDownList($modelDetail->getDocumentTypes(),['prompt' => '']) ?>
                </div>
                <div class="col-2">
                    <?=$form->field($model, 'DocumentNumber') ?>
                </div>
                <div class="col-2">
                    <?= $form->field($model, 'IdGenderType')->dropDownList($model->getGenderTypes(),['prompt' => '']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php // echo $form->field($model, 'MarriedName') ?>


    <?php // echo $form->field($model, 'IdIdentificationType') ?>

    <?php // echo $form->field($model, 'IdentificationNumber') ?>

    <?php // echo $form->field($model, 'IdGenderType') ?>

    <?php // echo $form->field($model, 'IdState') ?>


    <?php ActiveForm::end(); ?>

</div>
<?php
$js = <<< JS
    $(document).ready(function(){
        $("#btn-reset-search").on('click', function(){
            var frm = {};
            frm.ID = '$formName';
            frm.PREFIX = '$prefix-';
            clearForm(frm);
            $("#$formName").submit();
        });
    });
JS;
$this->registerJs($js);
?>