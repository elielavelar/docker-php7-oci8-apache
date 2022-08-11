<?php
use kartik\detail\DetailView;

/** @var $model \backend\models\Incidentrequestdetail */
/** @var $attributes array */
?>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => $attributes,
])?>
