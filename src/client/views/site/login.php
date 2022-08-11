<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use kartik\form\ActiveForm;

$this->title = 'Inicio de Sesión';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4">
        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h1 class="panel-title"><?= Html::encode($this->title) ?></h1>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12">
                        <?= $form->field($model, 'username',['addon'=>['prepend'=>['content'=>'<i class="fas fa-user"></i>']]])->textInput(['autofocus' => true,'autocomplete'=> 'off']) ?>
                        <?= $form->field($model, 'password',[
                                'addon'=>[
                                    'prepend'=>[
                                        'content'=> '<i class="fas fa-lock"></i>',
                                    ],
                                ]
                            ])->passwordInput() ?>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <?= Html::submitButton('Iniciar Sesión', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
