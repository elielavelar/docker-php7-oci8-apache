<?php

use yii\bootstrap4\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;

/* @var $this yii\web\View */
/* @var $model \common\models\prddui\Anexoacta */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="anexoacta-search">

    <?php $form = ActiveForm::begin([
        'id' => $formName,
    ]); ?>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <?= $form->field($model, 'FEC_FACTURACION')->widget(DatePicker::class, [
                            'language'=>'es',
                            'readonly'=> true,
                            'options' => ['placeholder' => 'Fecha de Acta...'],
                            'pluginOptions'=>[
                                'format'=>'dd-mm-yyyy',
                                'todayHighlight'=>true,
                                'autoclose'=>true,
                            ],
                        ]);
                    ?>
                </div>
                <div class="col-7">
                    <div class="form-group">
                        <?= Html::button('<i class="fas fa-search"></i> Consultar', ['type' => 'button', 'class' => 'btn btn-primary btn-lg', 'id' => 'btn-filter']) ?>
                        <?= Html::resetButton('<i class="fas fa-times"></i> Limpiar', ['class' => 'btn btn-outline-secondary btn-lg', 'id' => 'btn-reset']) ?>
                    </div>
                </div>
                <?= $form->field($model, 'COD_CTRO_SERV')->hiddenInput()->label(false) ?>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <label id="message"></label>
                </div>
            </div>
        </div>
    </div>

    

    <?php ActiveForm::end(); ?>

</div>

