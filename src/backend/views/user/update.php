<?php

use kartik\helpers\Html;
use yii\bootstrap4\Tabs;
use yii\bootstrap4\ActiveForm;
use yii\helpers\StringHelper;
/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $attachmentModel \common\models\Attachment */

$this->title = 'Actualizar Usuario: ' . $model->DisplayName;
$this->params['breadcrumbs'][] = ['label' => 'Usuarios', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->DisplayName, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Actualizar';

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
            'options'=>['enctype'=>'multipart/form-data'],
        ]); ?>
        <?= Tabs::widget([
                'items' => [
                    [
                        'label' => 'General',
                        'content' => $this->render('_form', [
                                'model' => $model,'form'=>$form,
                                'modelDetail' => $attachmentModel,
                        ]),
                        'active' => true
                    ],
                    [
                        'label' => 'Configuración ',
                        'content' => $this->render('_form/_formDetail',[
                            'model'=>$model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail
                            ]),
                        #'active' => true
                    ],
                ]]);
         ?>
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
