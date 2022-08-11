<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Catalogdetailvalues */

$this->title = 'Create Catalogdetailvalues';
$this->params['breadcrumbs'][] = ['label' => 'Catalogdetailvalues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="catalogdetailvalues-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
