<?php

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;
/* @var $model \common\models\LoginForm */
/* @var $this \yii\web\View */
$this->title = '';
?>
<?php $form = ActiveForm::begin(['id' => 'login-form']) ?>

<div class="card">
    <div class="card-body login-card-body">
        <p>Ingrese sus credenciales para iniciar sesi&oacute;n</p>
        <div class="row">
            <div class="col-12">
                <?=
                        $form->field($model, 'username', [
                            'options' => ['class' => 'form-group has-feedback'],
                            'inputTemplate' => '{input}<div class="input-group-append"><div class="input-group-text"><span class="fas fa-user"></span></div></div>',
                            'template' => '{beginWrapper}{input}{error}{endWrapper}',
                            'wrapperOptions' => ['class' => 'input-group mb-3']
                        ])
                        ->label(false)
                        ->textInput(['placeholder' => $model->getAttributeLabel('username')])
                ?>

                <?=
                        $form->field($model, 'password', [
                            'options' => ['class' => 'form-group has-feedback'],
                            'inputTemplate' => '{input}<div class="input-group-append"><div class="input-group-text"><span class="fas fa-lock"></span></div></div>',
                            'template' => '{beginWrapper}{input}{error}{endWrapper}',
                            'wrapperOptions' => ['class' => 'input-group mb-3']
                        ])
                        ->label(false)
                        ->passwordInput(['placeholder' => $model->getAttributeLabel('password')])
                ?>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="icheck-primary">
                    <?= $form->field($model, 'rememberMe')->checkbox() ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <?= Html::submitButton('Iniciar Sesión', ['class' => 'btn btn-primary btn-block']) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end() ?>