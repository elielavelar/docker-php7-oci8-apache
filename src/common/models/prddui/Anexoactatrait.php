<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models\prddui;

use Yii;
use Exception;
use yii\helpers\StringHelper;
use yii\db\Query;
/**
 *
 * @author avelare
 */
trait Anexoactatrait {
    private static $header;
    private static $detail = '';
    private static $detailvalues = [];
    private static $textHeader = '';
    private static $model = null;
    private static $date = null;
    public static function getReport($criteria = []){
        try {
            self::$model = Anexoacta::find()
                    ->where($criteria)
                    ->one();
            if(empty(self::$model)){
                throw new Exception('Datos no encontrados', 99000);
            }
            self::_setHeaderValues();
            self::_getDetail();
            return ['HEADER' => self::$header, 'DETAIL' => self::$detail];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _setHeaderValues(){
        try {
            self::$textHeader = "En el DUICENTRO ";
            self::$header['DEPTO'] = (self::$model->COD_CTRO_SERV ? 
                    (self::$model->ctroServ->COD_PAIS == 'SV' ? 'Departamento de ':'Estado de '):'Estado de ')
                    .StringHelper::mb_ucwords(strtolower(self::$model->ctroServ->department->NOM_DEPTO));
            self::$header['MUNIC'] = (self::$model->COD_CTRO_SERV ? 
                    (self::$model->ctroServ->COD_PAIS == 'SV' ? 'del Municipio de ':'de la Ciudad de '):'de la Ciudad de ')
                    .StringHelper::mb_ucwords(strtolower(self::$model->ctroServ->municipality->NOM_MUNIC));
            self::$date = \DateTime::createFromFormat('d-m-Y H:i:s', self::$model->FEC_ACTA);
            $recordDate = \DateTime::createFromFormat('d-m-Y', self::$model->FEC_FACTURACION);
            $nomchiefrole = self::$model->chief->COD_ROL == \backend\models\sdms\DatosOper::COD_CHIEF ? 'Jefe de Duicentro':'Asistente de Jefe de Duicentro';
            $nomofficerrole = 'Delegado del RNPN';
            self::$header['HOUR'] = Yii::$app->formatter->asSpellout(self::$date->format('H'));
            self::$header['MINUTES'] = Yii::$app->formatter->asSpellout(self::$date->format('i'));
            self::$header['DAY'] = Yii::$app->formatter->asSpellout(self::$date->format('d'));
            self::$header['MONTH'] = StringHelper::mb_ucfirst((new \client\components\LocaleDateFormat('MMMM'))->localeFormat(Yii::$app->language, self::$date));
            self::$header['YEAR'] = self::$date->format('Y');
            self::$header['NOMYEAR'] = Yii::$app->formatter->asSpellout(self::$date->format('Y'));
            $nomfec = self::$header["DAY"]." de ".self::$header["MONTH"]." del año ".self::$header["NOMYEAR"];
            self::$header['NOMFEC_FACTURACION'] = $nomfec;
            $nomdate = (self::$date->format('d-m-Y') == $recordDate->format('d-m-Y') ? 'esta fecha': $nomfec);
            self::$header['NOMDATE'] = $nomdate;
            self::$header['CHIEFROLE'] = $nomchiefrole;
            self::$header['OFFICERROLE'] = $nomofficerrole;
            self::$header['TOTAL_LETRAS_PV'] = Yii::$app->formatter->asSpellout(self::$model->PRIMERAVEZ);
            self::$header['TOTAL_LETRAS_MO'] = Yii::$app->formatter->asSpellout(self::$model->MODIFICACIONES);
            self::$header['TOTAL_LETRAS_RP'] = Yii::$app->formatter->asSpellout(self::$model->REPOSICIONES);
            self::$header['TOTAL_LETRAS_REIMP'] = Yii::$app->formatter->asSpellout(self::$model->REIMPRESIONES);
            self::$header['TOTAL_LETRAS_RN'] = Yii::$app->formatter->asSpellout(self::$model->RENOVACIONES);
            self::$header['TAR_BASE_ANULADAS_LETRAS'] = Yii::$app->formatter->asSpellout(self::$model->TAR_BASE_ANULADAS);
            self::$header['TAR_DECAD_ANULADAS_LETRAS'] = Yii::$app->formatter->asSpellout(self::$model->TAR_DECAD_ANULADAS);
            self::$header['NOMCHIEF'] = StringHelper::mb_ucwords(mb_strtolower(self::$model->chief->getCompleteName()));
            self::$header['NOMOFFICER'] = StringHelper::mb_ucwords(mb_strtolower(self::$model->officer->getCompleteName()));
            self::$header['DESC_CTRO_SERV'] = self::$model->ctroServ->DESC_CTRO_SERV;
            self::$header = array_merge(self::$model->attributes, self::$header);
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private static function _getDetail(){
        try {
            self::_getDetailValues();
            self::$detail = '';
            $c = 1;
            foreach (self::$detailvalues as $detail){
                $det = "<tr>";
                $det .= "<td></td>";
                $det .= "<td>$c</td>";
                $det .= "<td style='text-align:center'>".$detail['TIPO_TRAM']."</td>";
                $det .= "<td>".str_pad($detail['FOLIO_DUI'],8,'0',STR_PAD_LEFT)."</td>";
                $det .= "<td>".$detail['NUDUI']."</td>";
                $det .= "<td style='text-align:center;'>".$detail['CONTEO_TRAM']."</td>";
                $det .= "<td></td>";
                $det .= "</tr>";
                self::$detail .= $det;
                $c++;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    
    private static function _getDetailValues(){
        try {
            $sql = "select NUMERO_TARJETA FOLIO_DUI, LPAD(NUM_TARJETA,8,'0') Numero_Tarjeta,
			FN_STRNUDUI(b.nudui) nudui,c.tpo_tram,
			CASE
				  WHEN TPO_TRAM = 1 THEN 'PV-'||CONTEO_TRAM
				  WHEN TPO_TRAM = 2 THEN 'MO-'||CONTEO_TRAM
				  WHEN TPO_TRAM = 3 AND (COD_MOTIVO_REPO <=10 OR COD_MOTIVO_REPO IS NULL) THEN 'RP-'||CONTEO_TRAM
				  WHEN TPO_TRAM = 4 THEN 'RN-'||CONTEO_TRAM
				  WHEN TPO_TRAM = 3 AND COD_MOTIVO_REPO > 10 THEN 'RI-'||CONTEO_TRAM
			END TIPO_TRAM
			,NVL(to_number(c.cod_motivo_repo),1) cod_motivo_repo, FN_SIGUIENTE_TRAMITE(b.nudui,c.num_solic,a.fecha) as conteo_tram,c.num_solic
			from detalle_tarjetas_anuladas a, tarjetas b,tram_vigente c
			where a.cod_ctro_serv = :pr_codctroserv
			  and a.fecha = :pr_proddate
			  and a.cod_cons in ('AMP-00102', 'AMP-00103', 'AMP-00104')
			  and b.cod_ctro_serv = a.cod_ctro_serv
			  and b.fecha = a.fecha
			  and b.num_tarjeta = a.numero_tarjeta
			  and b.tipo_mov    = 6
			  and b.nudui          > 0
			  and c.nudui         = b.nudui
			  and c.num_folio_dui = b.num_tarjeta
                    UNION
			select NUMERO_TARJETA FOLIO_DUI, LPAD(NUM_TARJETA,8,'0') Numero_Tarjeta, 
			FN_STRNUDUI(b.nudui) nudui,c.tpo_tram,
			CASE
			  WHEN TPO_TRAM = 1 THEN 'PV-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 2 THEN 'MO-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 3 AND (COD_MOTIVO_REPO <=10 OR COD_MOTIVO_REPO IS NULL) THEN 'RP-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 4 THEN 'RN-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 3 AND COD_MOTIVO_REPO > 10 THEN 'RI-'||CONTEO_TRAM
			END TIPO_TRAM
			,NVL(to_number(c.cod_motivo_repo),1) cod_motivo_repo, 
                        CASE
			  WHEN TPO_TRAM = 1 THEN 'PV-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 2 THEN 'MO-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 3 AND (COD_MOTIVO_REPO <=10 OR COD_MOTIVO_REPO IS NULL) THEN 'RP-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 4 THEN 'RN-'||CONTEO_TRAM
			  WHEN TPO_TRAM = 3 AND COD_MOTIVO_REPO > 10 THEN 'RI-'||CONTEO_TRAM
			END conteo_tram, c.num_solic
			from detalle_tarjetas_anuladas a, tarjetas b,tram_vigente c
			where a.cod_ctro_serv = :pr_codctroserv
			  and a.fecha = :pr_proddate
			  and a.cod_cons in ('AMP-00102', 'AMP-00103', 'AMP-00104')
			  and b.cod_ctro_serv = a.cod_ctro_serv
			  and b.fecha = a.fecha
			  and b.num_tarjeta = a.numero_tarjeta
			  and b.tipo_mov    = 6
			  and b.nudui         > 0
			  and c.nudui         = b.nudui
			  --and c.num_folio_dui = b.num_tarjeta
			   and numero_tarjeta not in (select num_folio_dui from tram_vigente  where nudui > 0 and nudui =c.nudui and num_folio_dui is not null)
			   and c.num_solic = (select Num_Solic from pers_nat_dui where nudui > 0 and nudui = c.nudui and num_solic = c.num_solic)
			union
			select NUMERO_TARJETA FOLIO_DUI, LPAD(NUM_TARJETA,8,'0') Numero_Tarjeta, FN_STRNUDUI(b.nudui) nudui,0,
			'' tpo_tram, 0 cod_motivo_repo, '',0
			from detalle_tarjetas_anuladas a, tarjetas b
			where a.cod_ctro_serv = :pr_codctroserv
			  and a.fecha = TO_DATE(:pr_proddate, 'DD-MM-YYYY')
			  and a.cod_cons in ('AMP-00102', 'AMP-00103', 'AMP-00104')
			  and b.cod_ctro_serv = a.cod_ctro_serv
			  and b.fecha = a.fecha
			  and b.num_tarjeta = a.numero_tarjeta
			  and b.tipo_mov    = 6
			  and nudui = 0
			order by 4, 6, 1";
            $query = self::getDb()->createCommand($sql, [
                ':pr_proddate' => self::$date->format('d-m-Y'),
                ':pr_codctroserv' => self::$model['COD_CTRO_SERV'],
            ]);
            self::$detailvalues = $query->queryAll();
            
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
