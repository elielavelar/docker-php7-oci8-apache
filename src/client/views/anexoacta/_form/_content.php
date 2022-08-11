<?php

/* 
 *@var $model Array
 */
$title = 'DUICENTRO '.$model['HEADER']['DESC_CTRO_SERV'].'&nbsp;&nbsp;ANEXO ACTA N° '.$model['HEADER']['YEAR']."-".$model['HEADER']["NUM_CORR_ACTA"];

$header = "En el DUICENTRO ". $model['HEADER']["MUNIC"].", ".$model['HEADER']["DEPTO"].", a las ".$model['HEADER']["HOUR"]." horas con ".$model['HEADER']["MINUTES"].
		" minutos del día ".$model['HEADER']['NOMFEC_FACTURACION'].". Reunidos por una parte ".(trim($model['HEADER']["NOMCHIEF"])).
		", actuando en calidad de ".$model['HEADER']["CHIEFROLE"]." de MÜHLBAUER ID SERVICES GMBH de este DUICENTRO, y por otra, ".($model['HEADER']["NOMOFFICER"]).
		", actuando como Delegado del Registro Nacional de las Personas Naturales, con el objeto de establecer y determinar el trabajo realizado en el DUICENTRO durante la jornada correspondiente ".$model['HEADER']["NOMDATE"].
		", se procede para tales efectos a detallar el número de tarjetas decadáctilares y tarjetas base anuladas y entregadas al Delegado del RNPN: ".($model['HEADER']['TAR_DECAD_ANULADAS'] == 1 ? 'Tarjeta Decadactilar': 'Tarjetas Decadactilares').": ".
		 $model['HEADER']["TAR_DECAD_ANULADAS_LETRAS"]." (".$model['HEADER']["TAR_DECAD_ANULADAS"]."). ".($model['HEADER']['TAR_BASE_ANULADAS'] == 1 ? 'Tarjeta Base': 'Tarjetas Base').": ".$model['HEADER']["TAR_BASE_ANULADAS_LETRAS"]." (".$model['HEADER']["TAR_BASE_ANULADAS"]."); de las cuales: ".$model['HEADER']["TOTAL_LETRAS_PV"]." (".$model['HEADER']["PRIMERAVEZ"].")".
		 " son de Primera Vez, ".$model['HEADER']["TOTAL_LETRAS_MO"]." (".$model['HEADER']["MODIFICACIONES"].") son de Modificación, ".$model['HEADER']["TOTAL_LETRAS_RP"]." (".$model['HEADER']["REPOSICIONES"].") son de Reposición, ".
		$model['HEADER']["TOTAL_LETRAS_RN"]." (".$model['HEADER']["RENOVACIONES"].") son de Renovación, ".$model['HEADER']["TOTAL_LETRAS_REIMP"]." (".$model['HEADER']["REIMPRESIONES"].") son de Reimpresión.";
?>
<div class="card">
    <div class="card-body text-justify">
        <h4 style="text-align: center; margin-bottom: 10px"><b><?=$title?></b></h4>
        <p style="font-size: 10pt; margin-bottom: 0px">
            <?=$header?>
        </p>
        <p style="font-size: 10pt; z-index: 500; margin-top: 0px; margin-bottom: 0px !important">
            <label>Detalle:</label>
            <section style="width: 100%">
                <table class="simple-table" style="width: 100%; margin-left: 15pt">
                    <thead>
                        <tr>
                            <th colspan="6" style="text-align: left; padding-left: 15px">
                                <label>Tarjetas Decadáctilares</label>&nbsp;&nbsp;&nbsp;
                                <label>Tipo de Trámite de tarjeta</label>&nbsp;&nbsp;&nbsp;
                                <label>Tarjetas Base</label>
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 170px;text-align: center">No. de Folio</th>
                            <th style="width: 30px"></th>
                            <th style="width: 105px">anulada</th>
                            <th style="width: 100px">No. de Folio</th>
                            <th style="width: 90px">No. de DUI</th>
                            <th>Tipo de Trámite realizado</th>
                            <th style="width: 60px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?=$model['DETAIL']?>
                    </tbody>
                </table>
            </section>
        </p>
    </div>
    <div class="card-footer">
        <p style="margin-top: 25px; margin-left: 35px; margin-right: 35px; ">
            No teniendo nada más que hacer constar, estando conformes con lo consignado en la presente acta, la ratificamos, firmamos y sellamos en tres ejemplares originales.
        </p>
        <section style="width: 100%; margin-top: 50px">
            <table style="text-align: center; width: 100%">
                <tbody>
                    <tr>
                        <td>__________________________________________</td>
                        <td>__________________________________________</td>
                    </tr>
                    <tr>
                        <td><?=$model['HEADER']['NOMCHIEF']?></td>
                        <td><?=$model['HEADER']['NOMOFFICER']?></td>
                    </tr>
                    <tr>
                        <td><?=$model['HEADER']['CHIEFROLE']?></td>
                        <td><?=$model['HEADER']['OFFICERROLE']?></td>
                    </tr>
                </tbody>
            </table>
        </section>
    </div>
</div>