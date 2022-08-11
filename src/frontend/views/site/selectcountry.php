<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;
/* @var $model \common\models\CountryForm */
/* @var $this \yii\web\View */
$this->title = '';
?>
<?php $form = ActiveForm::begin(['id' => 'country-form']) ?>

        <div class="card-body login-card-body">
            <p>Seleccione Pa&iacute;s</p>
            <div class="row">
                <div class="col-12">
                    <?=
                    $form->field($model, 'idcountry', [])
                        ->label(false)
                        ->dropDownList($model->getCountries(),[])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= Html::submitButton('Aceptar', ['class' => 'btn btn-primary btn-block']) ?>
                </div>
            </div>
        </div>
<?php ActiveForm::end() ?>