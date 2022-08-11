<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Appointments */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Sistema de Registro de Citas';
$form = ActiveForm::begin();
?>
<div class="site-index">
    <div class="body-content">
        <div class="box">
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Citas Registradas</h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="box">
                        <h2>Registro de Cita</h2>
                        <form class="" role="search">
                        <div class="row">
                            <div class="col-lg-6">
                                <?= $form->field($model, 'IdServiceCentre')->dropDownList($model->getServiceCentres(),['prompt'=>'--SELECCIONE DUICENTRO--']) ?>
                            </div>
                            <div class="col-lg-6">
                                <?=$form->field($model, 'AppointmentDate');?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-6">
                                <button type="button" class="btn btn-default" id="btnSave" name="btnSave">
                                    <i class="fa fa-save"></i>&nbsp;Registrar
                                </button>
                            </div>
                            
                        </div>
                        
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    ActiveForm::end();
?>