<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;
/* @var $model \common\models\CompanyForm */
/* @var $this \yii\web\View */
$this->title = '';
?>
<?php $form = ActiveForm::begin(['id' => 'company-form']) ?>

    <div class="card">
        <div class="card-body login-card-body">
            <p>Seleccione Organizaci&oacute;n</p>
            <div class="row">
                <div class="col-12">
                    <?=
                    $form->field($model, 'idcompany', [])
                        ->label(false)
                        ->dropDownList($model->getCompanies(),[])
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?= Html::submitButton('Aceptar', ['class' => 'btn btn-primary btn-block']) ?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end() ?>