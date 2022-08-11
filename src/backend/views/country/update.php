<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
/* @var $this yii\web\View */
/* @var $model common\models\Country */

$this->title = 'Actualizar País: ' . $model->Name;
$this->params['breadcrumbs'][] = ['label' => 'Países', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Name, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('app','Update');
?>
<div class="countries-update">

    <div class="card">
        <div class="card-header bg-primary">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin(); ?>

        <?= $this->render('_form', [
            'model' => $model,'form'=>$form
        ]) ?>
        <?php ActiveForm::end(); ?>
    </div>

</div>
