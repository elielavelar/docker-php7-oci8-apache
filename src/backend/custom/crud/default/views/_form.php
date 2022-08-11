<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use kartik\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */
?>
<?= "<?php " ?>$form = ActiveForm::begin([]); ?>
<div class="card-body">
<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo "  <div class='row'>\n";
        echo "      <div class='col-6'>\n";
        echo "          <?= " . $generator->generateActiveField($attribute) . " ?>\n";
        echo "      </div>\n";
        echo "  </div>\n";
    
    }
} ?>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-12">
            <span class="float-right">
                <?= "<?= " ?>Html::submitButton(<?= $generator->generateString('Guardar') ?>, ['class' => 'btn btn-success']) ?>
            </span>
        </div>
    </div>
</div>
<?= "<?php " ?>ActiveForm::end(); ?>
