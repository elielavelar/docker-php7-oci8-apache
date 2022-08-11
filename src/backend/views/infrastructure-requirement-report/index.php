<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $models backend\models\Options */

$this->title = "Reportes de Infraestructura";
$this->params['breadcrumbs'][] = 'Reportes de Infraestructura';
$this->params['breadcrumbs'][] = $this->title;

?>
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row">
				<div class="col-md-5">
					<?php $form = ActiveForm::begin(['id' => 'registration-form']); ?>
			      <?= $form->field($model, 'name') ?>			          
			      <div class = "form-group">
			         <?= Html::submitButton('Generar Reporte', ['class' => 'btn btn-primary',
			            'name' => 'registration-button']) ?>
			      </div>
			      <?php ActiveForm::end(); ?>
				</div>
			</div>
		</div>
	</div>