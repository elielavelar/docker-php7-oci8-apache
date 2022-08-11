<?php
use kartik\grid\GridView;
use kartik\helpers\Html;
use common\models\Extendedmodelfield;
/* @var $this yii\web\View */
/* @var $model \common\models\Extendedmodelfieldgroup */

$modelDetail = new Extendedmodelfield();
$modelDetail->IdExtendedModelFieldGroup = $model->Id;
$controller = 'extendedmodel';
$update = Yii::$app->customFunctions->userCan($controller.'Update');
$delete = Yii::$app->customFunctions->userCan($controller.'Delete');
?>
<div class="row-fluid">
    <div class="col-12">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="active" style="width: 5%"><?=$modelDetail->getAttributeLabel('Sort');?></th>
                    <th class="active" style="width: 20%"><?=$modelDetail->getAttributeLabel('IdField');?></th>
                    <th class="active" style="width: 24%"><?=$modelDetail->getAttributeLabel('CustomLabel')?></th>
                    <th class="active" style="width: 10%"><?=$modelDetail->getAttributeLabel('Required')?></th>
                    <th class="active" style="width: 5%"><?=$modelDetail->getAttributeLabel('ColSpan')?></th>
                    <th class="active" style="width: 5%"><?=$modelDetail->getAttributeLabel('RowSpan')?></th>
                    <th class="active" style="width: 15%"><?=$modelDetail->getAttributeLabel('CssClass')?></th>
                    <th class="active text-right">
                        <?=Html::a('<i class="fas fa-plus-circle"></i> Agregar Campo',"javascript:addField($model->Id);",['class' => 'btn btn-success btn-sm']);?>
                    </th>
                </tr>
            </thead>
            <tbody>
            <?php if(count($model->extendedmodelfields) > 0) : ?>
                <?php foreach ($model->extendedmodelfields as $field): ?>
                    <tr>
                        <td><?=$field->Sort;?></td>
                        <td><?=($field->IdField ? $field->field->Name : '');?></td>
                        <td><?=$field->CustomLabel; ?></td>
                        <td><?=$field->Required == $modelDetail::REQUIRED_ENABLED ? 'Sí':'No'; ?></td>
                        <td><?=$field->ColSpan; ?></td>
                        <td><?=$field->RowSpan; ?></td>
                        <td><?=$field->CssClass; ?></td>
                        <td class="text-left">
                            <?=($update ? Html::a("<span style='margin-right:5pt'><i class='fas fa-pen-square fa-lg'></i></span>", "javascript:editField($field->Id);", ['title'=>'Editar Campo']) :"") ?>
                            <?=($delete ? Html::a("<span style='margin-left:10pt; margin-right:5pt'><i class='fas fa-trash-alt fa-lg'></i></span>", "javascript:deleteField($field->Id);", ['title'=>'Eliminar Campo']) :"") ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No se encontraron Registros</td>
                    </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>