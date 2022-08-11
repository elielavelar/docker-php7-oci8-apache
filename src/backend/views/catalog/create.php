<?php
use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Catalog */

$this->title = Yii::t('app', 'Add').' '.Yii::t('system', 'Catalog');
$this->params['breadcrumbs'][] = ['label' => Yii::t('system', 'Catalogs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
    <div class="card-header bg-primary">
        <h4 class="card-title"><?= Html::encode($this->title) ?></h4>
    </div>
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
