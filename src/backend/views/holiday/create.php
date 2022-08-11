<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Holidays */

$this->title = 'Crear Día Feriado';
$this->params['breadcrumbs'][] = ['label' => 'Días Feriados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header">
        <h4><?= Html::encode($this->title) ?></h4>
    </div>
    <?php $form = ActiveForm::begin(); ?>
    <?= $this->render('_form', [
        'model' => $model, 'form'=>$form,
    ]) ?>
    <?php ActiveForm::end(); ?>
</div>
