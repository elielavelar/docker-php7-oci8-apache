<?php

use yii\helpers\Html;
use kartik\form\ActiveForm;
use yii\bootstrap\Tabs;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = 'Crear Usuario';
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$parentName = StringHelper::basename($model->className());
$tableName = 'user';
$formName = $tableName.'-form';
?>
<div class="user-update">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?php $form = ActiveForm::begin([
            'id'=>$formName,
        ]); ?>
        <?= $this->render('_form', ['model' => $model,'form'=>$form]); ?>
        <div class="card-footer">
            <div class="row">
                <div class="col-12">
                    <div class="form-group float-right">
                        <?= Html::submitButton('<i class="fas fa-save"></i> Guardar', ['class' => 'btn btn-success']) ?>
                        <?= Html::a('<i class="fas fa-times"></i> Cancelar', ['index'], ['class'=>'btn btn-danger'])?>
                    </div>
                </div>
                
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
