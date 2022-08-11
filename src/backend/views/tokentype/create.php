<?php

use yii\helpers\Html;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model backend\models\TokenType */

$this->title = 'Agregar Tipo de Token';
$this->params['breadcrumbs'][] = ['label' => 'Tipos de Token', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$formName = $model->tableName().'-form';
?>
<div class="token-type-create">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
        </div>
        <?= $this->render('_form', [
            'model' => $model, 'formName' => $formName,
        ]) ?>
    </div>
</div>
<?php
$js = <<< JS
   $(document).ready(function(){
       
   });
JS;
$this->registerJs($js, View::POS_READY);
?>
