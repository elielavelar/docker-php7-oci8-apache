<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Process */

$this->title = 'Crear Proceso';
$this->params['breadcrumbs'][] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Procesos', 'url' => ['index']];
?>
<div class="card">
    <div class="card-header">
        <h4 class="card-title"><?= Html::encode($this->title);?></h4>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?= $this->render('_form', [
        'model' => $model, 'form' => $form
    ]) ?>
    <div class="card-footer">
        <div class="row">
            <div class="col-12">
                <span class="float-right">
                    <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                    <?= Html::a('<i class="fas fa-times"></i> Cancelar',['index'],['class'=> 'btn btn-danger']);?>
                </span>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
