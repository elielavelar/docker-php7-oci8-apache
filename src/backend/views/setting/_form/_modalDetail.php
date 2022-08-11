<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Settingsdetail */
/* @var $form yii\widgets\ActiveForm */
$tableName = $model->tableName();
?>
<div class="modal fade in" id="modal-<?=$tableName?>" tabindex="-1" role="modal" aria-labeledby="#Label">
    <div class="modal-dialog modal-title modal-lg" role="document">
        <div class="modal-content ">
            <div class="modal-header bg-primary" >
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true"><a class="glyphicon glyphicon-remove" style="color: white"></a></span></button>
                <h3 class="modal-title" id="Label"><strong>Detalle de Par&aacute;metro</strong></h3>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <?=$this->render('_formDetail', [
                        'model' => $model,
                    ])?>
                </div>
                <div class="modal-footer">
                    <div class="row">
                        <div class="col-12">
                            <div class="float-right">
                                <?= Html::button('<i class="fas fa-save"></i> Guardar', ['type' => 'button', 'id' => 'btn-save-alt','class' => 'btn btn-success']) ?>
                                <?= Html::button('<i class="fas fa-times"></i> Cancelar', ['type' => 'button', 'id'=>'btn-cancel-alt','class' => 'btn btn-danger']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>