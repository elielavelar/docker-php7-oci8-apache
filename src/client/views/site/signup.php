<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Registro de Ciudadano';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="form-group">
    <div class="site-signup">
        <h1><?= Html::encode($this->title) ?></h1>

        <p class="graycolor">Favor completar los siguientes campos para autenticaci&oacute;n:</p>

        <div class="row">
            <div class="col-lg-4">
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                    <?= $form->field($model, 'name')->textInput(['autofocus' => true,'style'=>'text-transform: uppercase']) ?>
                    <?= $form->field($model, 'lastname')->textInput(['style'=>'text-transform: uppercase']) ?>
                    <?= $form->field($model, 'email') ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>
                    <?= $form->field($model, 'passwordconfirm')->passwordInput() ?>

                    <div class="form-group">
                        <?= Html::submitButton('Registrarse', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-lg-1">
                
            </div>
            <div class="col-lg-5">
                <div class="col-lg-12">
                    <div class="list-group">
                        <a href="#" class="list-group-item active" style="text-align: center">
                            <img src="<?= Yii::$app->params["mail"]["image"] ?>" title="fingerprint" style="" alt="">
                            <h4 class="list-group-item-heading">Pasos para Registrar una Cita</h4>
                        </a>
                        <a href="#" class="list-group-item">
                          <h4 class="list-group-item-heading">Paso 1</h4>
                          <p class="list-group-item-text">Crea tu Usuario</p>
                        </a>
                        <a href="#" class="list-group-item">
                          <h4 class="list-group-item-heading">Paso 2</h4>
                          <p class="list-group-item-text">Confirma tu Usuario</p>
                        </a>
                        <a href="#" class="list-group-item">
                          <h4 class="list-group-item-heading">Paso 3</h4>
                          <p class="list-group-item-text">Registra tu Cita</p>
                        </a>
                    </div>
                </div>
                <div class="col-lg-12">
                    <img src="<?=  Url::to("@web/img/finger.jpg");?>" title="fingerprint" alt="">
                </div>
                
            </div>
        </div>
    </div>
</div>
