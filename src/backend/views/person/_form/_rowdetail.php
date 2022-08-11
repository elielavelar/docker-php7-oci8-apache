<?php
/* @var $this yii\web\View */
/* @var $model common\models\Person */
?>
<div class="card">
    <div class="card-body">
        <?php 
            foreach ($model->personaldocuments as $doc):
        ?>
        <div class="row">
            <div class="col-12">
                <?=$doc->IdDocumentType ? $doc->documentType->Name : ''?> : 
                <?=$doc->DocumentNumber?>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>