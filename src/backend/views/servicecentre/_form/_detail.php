<?php

use yii\helpers\Html;
use yii\helpers\StringHelper;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/* @var $model \common\models\Servicecentres */
/* @var $modelDetail backend\models\Appointmentservicesetting */
$weekdays = $modelDetail->weekdays;
unset($weekdays['1']);

$setting = [];
foreach ($model->appointmentservicesettings as $det){
    $setting[$det->IdDay][$det->IdHour]=['IdState' => $det->IdState,'CodState'=>$det->state->Code,'Quantity'=>$det->Quantity];
}
#echo '<pre>';
#print_r($setting);
#echo '</pre>';
?>
<div class="box">
    <div class="card">
        <div class="card-header">
            <div class="row">
                <span class="float-left">
                    <h4 class="card-title">Configuraci&oacute;n de Atenci&oacute;n de Citas de Ciudadanos</h4>
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="well-sm bg-info">
                    <i class="glyphicon glyphicon-info-sign"></i> Desmarque el control a la izquierda del campo para personalizar la cantidad de citas a atender por hora
                </div>
            </div>
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th style="width: 8%">Hora / D&iacute;a</th>
                        <?php 
                        foreach ($weekdays as $key => $value) {
                            echo '<th>'.$value. '</th>';
                        }
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tr = "";
                    $modelClass = StringHelper::basename($model->className());
                    $modelDetailClass = StringHelper::basename($modelDetail->className());
                        for($i = $modelDetail->start_time; $i <= $modelDetail->end_time; $i++){
                            $hour = DateTime::createFromFormat('H', $i);
                            $htime = $hour->format('H:i');
                            $tr = "<tr>";
                            $tr .= "<td>".$htime."</td>";
                            $val = 0;
                            $lock = TRUE;
                            foreach ($weekdays as $key => $value) {
                                if(isset($setting[$key])){
                                    $val = isset($setting[$key][$i]) ? $setting[$key][$i]['Quantity']:0;
                                    $lock = isset($setting[$key][$i]) ? ($setting[$key][$i]['CodState'] == 'INA' ? TRUE:FALSE):FALSE;
                                }
                                $tr .= '<td><div class="row-fluid">'
                                        . '<div class="col-1">'.Html::checkbox($modelClass."[".$modelDetailClass."][CHK][".$key."][$i]", $lock, ['class'=>'form-control-static checkbox hours']).'</div>'
                                        . '<div class="col-9">'.Html::textInput($modelClass."[".$modelDetailClass."][".$key."][$i]", $val, ['style'=>'width:100%','readonly'=>($lock ? 'readonly':FALSE),'class'=>'form-control ','title'=>$weekdays[$key]." ".$htime]).'</div>'
                                        . '</td>';
                            }
                            $tr .= "<tr>";
                            echo $tr;
                        }
                    ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
<?php
$script = <<< JS
   $(document).ready(function(){
        $('.hours').on('click',function(){
            var chk = $(this);
            var checked = chk.is(':checked') ? 1:0;
            var inp = chk.parents('div').parent('div.row-fluid').find('input[type=text]');
            var isreadonly = inp.attr('readonly');
            if(isreadonly){
                inp.removeAttr('readonly');
            } else {
                inp.attr('readonly',true)
                    .val('0');
            }
        });
   });
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>