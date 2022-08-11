<?php
use backend\models\Option;
use yii\helpers\StringHelper;
/* @var $this yii\web\View */
/* @var $model \common\models\Profile */
/* @var $searchModel backend\models\Profileoptions */
/* @var $form yii\widgets\ActiveForm */
$option = new Option();
$tableName =  'user';
$parentName = StringHelper::basename($model->className());
$childName = StringHelper::basename($searchModel->className());
$formName = $tableName.'-form';
?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h3>Detalle de Permisos</h3>
            </div>
            <div class="col-12">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="active" style="width: 2%"></th>
                            <th class="active" style="width: 2%"></th>
                            <th class="active" style="width: 2%"></th>
                            <th class="active" style="width: 2%"></th>
                            <th class="active" style="width: 26%"><?=$option->getAttributeLabel('Name')?></th>
                            <th class="active" style="width: 10%"><?=$option->getAttributeLabel('IdType')?></th>
                            <th class="active" style="width: 18%"><?=$option->getAttributeLabel('Url')?></th>
                            <th class="active" style="width: 12%"><?=$option->getAttributeLabel('KeyWord')?></th>
                            <th class="active" style="width: 10%"><?=$option->getAttributeLabel('IdState')?></th>
                            <th class="active" style="width: 5%"><?=$option->getAttributeLabel('ItemMenu')?></th>
                            <th class="active" style="width: 2%">Nivel</th>
                            <th class="active" style="width: 2%">Personalizar</th>
                            <th class="active" style="width: 5%"><?=$option->getAttributeLabel('Enabled')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            echo $searchModel->list;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php 
$script = <<< JS
    $(document).ready(function(){
        $('#$formName input[type=checkbox]').on('click',function(){
            var name = $(this).attr('name');
            var params = {};
            params.NAME = name;
            params.SEPARATORS = ["["];
            var nm = unboundName(params);
            var params = {};
            params.STRING = nm;
            params.REPLACESTRING = {']':''};
            name = replaceString(params);
            getParent(name);
        });
        
        $(".custom-level").on('click', function(){
            var control = $(this);
            var params = {};
            params.SELECTOR = control;
            params.TYPE = 'ARRAY';
            var data = getDataBind(params)
            var id = data['bind'];
            var _control = $("#"+id);
            params.SELECTOR = _control;
            var _data = getDataBind(params)
            if($("#"+id).is(':disabled')){
                $("#"+id).removeAttr('disabled');
                var enabled = _data['enabled'] == 1;
                if(enabled){
                    _control.attr('checked',enabled).prop('checked', enabled);
                } else {
                    _control.removeAttr('checked').prop('checked', false);
                }
                control.parents('tr').find('td.level').html(_data['custom']);
            } else {
                var enabled = _data['enabled'] == 1;
                var profile = _data['profile'];
                _control.attr('disabled',true);
                if(profile == 1){
                    _control.attr('checked',enabled).prop('checked', enabled);
                } else {
                    _control.removeAttr('checked').prop('checked', false);
                }
                
                control.parents('tr').find('td.level').html(_data['default']);
            }
        });
    });
        
    var getParent = function(id){
        
    };
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>