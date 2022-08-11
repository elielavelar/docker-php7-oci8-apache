<?php
use kartik\widgets\ActiveForm;
use kartik\password\PasswordInput;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
/* @var $this yii\web\View */
/* @var $model backend\models\sdms\DatosOper */
$verdictTitles = [
    0 => 'No Ingresada',
    1 => 'Muy Débil',
    2 => 'Débil',
    3 => 'Aceptable', 
    4 => 'Buena',
    5 => 'Excelente'
];
?>
<?php Modal::begin([
        'options' => [
            'id' => 'modal-viewpass',
        ],
        'title' => '<h3>Ver Contraseña Operador: '.$model->COD_OPER.'</h3>',
        'headerOptions' => ['class' => 'bg-primary'],
        'toggleButton' => ['label' => '<i class="far fa-eye-slash"></i> Ver Contraseña', 'class'=> 'btn btn-success'],
        'footer' => Html::button('<i class="fas fa-times-circle"></i> Cerrar', ['class'=> 'btn btn-danger', 'id'=> 'btnClose']),
    ])?>
    <?php $form = ActiveForm::begin([
        'id'=> 'viewpassword-form'
    ]); ?>
    <div class='row'>
        <div class="col-12">
            <?=$form->field($model, '_password')->widget(PasswordInput::className(), [
                'language'=>'es_SV',
                'pluginOptions'=>[
                    'showMeter'=>TRUE,
                    'verdictTitles' => $verdictTitles,
                ],
                'options' => [
                    'readonly' => true
                ],
            ]);?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php Modal::end();?>
<?php
$js = <<< JS
   $(document).ready(function(){
        $("#btnClose").on('click', function(){
            $("#modal-viewpass").modal("toggle");
        });
   });
JS;
$this->registerJs($js);
?>