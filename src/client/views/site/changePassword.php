<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\ChangePasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\password\PasswordInput;

$this->title = 'Cambio de Contraseña';
$this->params['breadcrumbs'][] = $this->title;
$verdictTitles = [
    0 => 'No Ingresada',
    1 => 'Muy Débil',
    2 => 'Débil',
    3 => 'Aceptable', 
    4 => 'Buena',
    5 => 'Excelente'
];
?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?= Html::encode($this->title) ?></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-5">
                <p>Favor, ingrese una nueva contrase&ntilde;a:</p>
                <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                    <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>
                    <?= $form->field($model, 'oldPassword')->widget(PasswordInput::className(),[
                        'language'=>'es_SV',
                        'pluginOptions'=>[
                            'showMeter'=>FALSE
                        ],
                    ]); ?>
                    <?= $form->field($model, 'newPassword')->widget(PasswordInput::className(),[
                        'language'=>'es_SV',
                        'pluginOptions'=>[
                            'verdictTitles'=> $verdictTitles,
                        ],
                    ]); ?>
                    <?= $form->field($model, 'confirmNewPassword')->widget(PasswordInput::className(), [
                        'language'=>'es_SV',
                        'pluginOptions'=>[
                            'showMeter'=>FALSE,
                        ],
                    ]);?>
                    <div class="form-group">
                        <?= Html::submitButton('Actualizar', ['class' => 'btn btn-primary']) ?>
                        <?= Html::a('Cancelar',['logout'],['class'=>'btn btn-danger'])?>
                    </div>

                <?php ActiveForm::end(); ?>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-6">
                <div class="bs-callout bs-callout-warning">
                    <h3>Pol&iacute;tica de Contrase&ntilde;as</h3>
                    <p>
                        <h4>1.1	Generalidades</h4>
                        <ul>
                            <li>Todo empleado de Mühlbauer ID Services, debe de hacer uso de las recomendaciones de esta pol&iacute;tica a fin de evitar incidentes de seguridad.</li>
                            <li>Toda contrase&ntilde;a por default (en dispositivos, servicios etc.), se deber&aacute; cambiar antes de utilizar dicho equipo/servicio en producci&oacute;n.</li>
                            <li>Hay que evitar utilizar secuencias b&aacute;sicas de teclado (por ejemplo: &quot;qwerty&quot;, &quot;asdf&quot;; o las t&quot;picas en numeraci&oacute;n: &quot;1234&quot; &oacute; &quot;98765&quot;)</li>
                                <li>
                                    Toda contrase&ntilde;a debe de tener como m&iacute;nimo 8 d&iacute;gitos, de los cuales:
                                        <ul>
                                            <li>Se debe de incluir caracteres en  May&uacute;sculas</li>
                                            <li>Se debe de incluir caracteres en  Min&uacute;sculas</li>
                                            <li>Se debe de incluir n&uacute;meros</li>
                                            <li>Se debe de incluir caracteres especiales</li>					
                                        </ul>
                                        <p>Ejemplo: M   k   3   9   1   *   +   a</p>
                                </li>
                                <li>Toda contrase&nt&ntilde;a debe de tener un periodos de rotaci&oacute;n no mayor a 4 meses</li>
                                <li>No utilizar palabras que se encuentren en diccionarios o de nombres comunes</li>
                                <li>Es necesario recordar la contrase&nt&ntilde;a. Y nunca debe ser escrita (a fin de recordarla)</li>
                                <li>No se debe utilizar la misma contrase&nt&ntilde;a en todos los accesos asignados, ejemplo: utilizar la misma contrase&ntilde;a para todas las aplicaciones que utiliza</li>
                                <li>No se debe divulgar la contrase&ntilde;a (esto incluye a: Superiores, compa&ntilde;eros o colaboradores m&aacute;s directos)</li>
                                <li>Toda gesti&oacute;n que se realice con sus credenciales (usuario / contrase&ntilde;a), quedar&aacute; registrada y es su responsabilidad directa</li>
                                <li>No escribir las contrase&ntilde;as en ordenadores de los que se desconozca su nivel de seguridad y puedan estar monitorizados, o en ordenadores de uso p&uacute;blico (demostraciones, Hoteles, cibercaf&eacute;s, Proveedores,  etc.)</li>
                                <li>No utilizar en ning&uacute;n caso contrase&ntilde;as que se ofrezcan en los ejemplos explicativos de construcci&oacute;n de contrase&ntilde;as robustas</li>
                                <li>Las contrase&ntilde;as no se deben enviar por correo electr&oacute;nico o en un sms, chat (seguro), etc. Tampoco se debe facilitar ni mencionar en una conversaci&oacute;n o comunicaci&oacute;n de cualquier tipo</li>
                                <li>No utilizar datos relacionados con el usuario que sean f&aacute;cilmente deducibles, o derivados de estos. (ej.: no poner como contrase&ntilde;a apodos, el nombre del actor o de un personaje de ficci&oacute;n preferido, etc.)</li>
                                <li>No se debe utilizar como contrase&ntilde;a, ni contener, el nombre de usuario asociado a la contrase&ntilde;a</li>

                        </ul>
                        <h4>Aplica a:</h4>
                        <ul>
                            <li>Aplicaciones con token f&iacute;sico (software y hardware)</li>
                            <li>Aplicaciones con token l&oacute;gico (usuario / Contrase&ntilde;as)</li>
                            <li>Usuario de sistema operativo de usuario y administrador</li>
                            <li>Usuario de correo electr&oacute;nico</li>
                            <li>Usuario de nube de M&uuml;hlbauer (soluci&oacute;n de cloud storage interna)</li>
                            <li>Dispositivos de impresi&oacute;n y otros</li>
                            <li>Dispositivos m&oacute;viles (laptops, tabletas, celulares y otros)</li>
                            <li>Intranet y Aplicativos web.</li>
                            <li>Todo aplicativo desarrollado o adquirido.</li>
                        </ul>

                        <h4>Excepciones:</h4>
                        <ul>
                                <li>Los únicos casos en que no aplican las características que debe cumplir una contraseña, es en aquellos cuyo hardware limite el uso únicamente de números, como por ejemplo: El acceso mediante los paneles de control de acceso y alarmas, o bien aquellas medidas las cuales tienen otros controles previos donde si se cumple dicha política.</li>
                        </ul>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
