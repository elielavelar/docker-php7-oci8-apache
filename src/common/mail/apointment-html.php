<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>
<div class="apointment-content">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title">Confirmaci&oacute;n de <?=$data['title']?> de Cita</h3>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <?=$data['message']?>
                </div>
            </div>
        </div>
    </div>
</div>
