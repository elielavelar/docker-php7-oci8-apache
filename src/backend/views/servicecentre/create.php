<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Servicecentre */

$this->title = 'Crear Centro de Atención';
$this->params['breadcrumbs'][] = ['label' => 'Centros de Atención', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="servicecentres-create">

    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin(); ?>
        <?= $this->render('_form', [
            'model' => $model, 'form'=>$form,
        ]) ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <span class="float-right">
                        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('Cancelar', ['index'] ,['class' => 'btn btn-danger']) ?>
                    </span>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

</div>
