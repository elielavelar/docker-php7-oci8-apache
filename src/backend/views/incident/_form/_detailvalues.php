<?php
use kartik\detail\DetailView;

/** @var $model \backend\models\Incidentdetail */
/** @var $attributes array */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => $attributes,
])?>
