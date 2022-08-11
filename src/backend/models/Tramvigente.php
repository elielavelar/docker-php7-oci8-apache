<?php

namespace backend\models;

use backend\models\CustomActiveRecord;
use yii\base\Exception;
use Yii;

/**
 * This is the model class for table "TRAM_VIGENTE".
 *
 * @property int $NUM_SOLIC
 * @property int $NUM_AFIS
 * @property int $NUDUI
 * @property int $NUM_FOLIO_DUI
 * @property string $STAT_TRAM
 * @property int $TPO_TRAM
 * @property string $COD_MOTIVO_REPO
 * @property string $FEC_REGIS
 * @property string $COD_OPER_REGIS
 * @property string $FEC_CAPT_IMG
 * @property string $COD_OPER_IMG
 * @property string $FEC_EMI_DUI
 * @property string $COD_OPER_EMI_DUI
 * @property string $COD_IMPR_EMI_DUI
 * @property string $FEC_ENTR_DUI
 * @property string $COD_OPER_ENTR_DUI
 * @property string $COD_CTRO_SERV
 * @property int $NUM_REC_PAGO
 * @property string $FEC_PAGO
 * @property int $COD_AGEN_BANCO
 * @property int $COD_BANCO
 * @property string $PRECIO
 * @property string $COD_SPRV_TRAM
 * @property string $STAT_AFIS_AUTORIZA
 * @property int $CONTEO_TRAM
 * @property int $ETAPA_TRAM
 * @property string $CND_HIT_REGIS
 * @property string $CND_HIT_CAPT_IMG
 * @property string $CND_HIT_VERIF
 * @property string $CND_HIT_ENTR
 * @property string $FEC_VENCE_DUI
 * @property string $OBSERVACIONES
 * @property string $STAT_TRAM_TEMP Indicador de enrolamiento
 * @property string $FEC_RECTIFICA Fecha de rectificacion
 * @property string $CONFIRMA Confirma la llegada de los archivos de minutiaes
 * @property int $NUM_SOLIC_INV Numero solicitud para las huellas invertidas en el AFIS
 * @property int $NUM_AFIS_INV Numero de Afis para las huellas invertidas
 * @property string $CND_HIT_REGIS_INV Hit de Huellas Invertidas
 * @property string $CND_HIT_VERIF_INV Hit de Estacion de Verificacion de huellas invertidas
 * @property string $COD_CTRO_QUEDAN Registra el ctro que extendio el quedan
 * @property string $TPO_DOC_PRESENT Registra el tipo de documento presentado recibo o quedan para obtener el DUI
 * @property string $CONF_IMAGENES Confirma los archivos de imagenes
 * @property string $FEC_ENTR_RECTIFICA Almacena la nueva fecha de entrega
 * @property string $COD_OPER_COMPLE Operador que completa la informacion del dia
 * @property string $COD_SUPERV_COMPLE Supervisor nocturno que verifica la digitacion
 * @property string $FEC_COMPLE Fecha en la cual se compplementa el registro
 * @property string $FEC_SUPERV_COMPLE Fecha en la cual se chequea el registro por el supervisor
 * @property int $PART_MARCADA Num Solicitud que tiene la partida que ya esta Marcada
 * @property string $COD_OPER_RECTIFICA Codigo del Capturista de Datos que rectifica el tramite
 * @property string $COD_DEL_RECTIFICA Codigo del Delegado que autoriza la rectificacion del tramite
 * @property string $COD_OPER_REIMP Codigo del Capturista de Imagen que hiso la reimpresion de un Dui
 * @property string $COD_DEL_REIMP Codigo del Delegado que autoriza la reimpresion de un Dui
 * @property string $COD_DEL_CORREC Codigo del Delegado que autoriza la correccion de un Dui
 * @property string $CALIDAD_IMAGEN Calidad de huella
 * @property int $NUM_REC_PAGO2 Numero de recibo de pago complementario
 * @property string $FEC_PAGO2 Fecha de pago del recibo complementario
 * @property int $COD_AGEN_BANCO2 Sucursal del banco del recibo complementario
 * @property int $COD_BANCO2 Codigo del banco del recibo complementario
 * @property string $PRECIO2 Precio del recibo complementario
 * @property string $COD_CTRO_SERV_RECTI
 * @property string $SERIE_RECIBO1 Serie con la cual se emiten preimpresos los recibos normales
 * @property string $SERIE_RECIBO2 Serie con la cual se emiten preimpresos los recibos complemento
 * @property string $FEC_HORA_INICIO Fecha de inicio del tramite del ciudadano, en informacion
 *
 * @property Tramvigentesdms $tramvigentesdms
 */
class Tramvigente extends CustomActiveRecord
{
    public static function getDb() {
        return Yii::$app->prdduitest;
    }
    
    public $Application_Id;
    public $dui;
    public $firstNames;
    public $lastNames;
    public $documentNumber;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.TRAM_VIGENTE';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['NUM_SOLIC', 'NUM_AFIS'], 'required'],
            [['NUM_SOLIC', 'NUM_AFIS', 'NUDUI', 'NUM_FOLIO_DUI', 'TPO_TRAM', 'NUM_REC_PAGO', 'COD_AGEN_BANCO', 'COD_BANCO', 'CONTEO_TRAM', 'ETAPA_TRAM', 'NUM_SOLIC_INV', 'NUM_AFIS_INV', 'PART_MARCADA', 'NUM_REC_PAGO2', 'COD_AGEN_BANCO2', 'COD_BANCO2'], 'integer'],
            [['PRECIO', 'PRECIO2'], 'number'],
            [['STAT_TRAM', 'STAT_AFIS_AUTORIZA', 'CND_HIT_REGIS', 'CND_HIT_CAPT_IMG', 'CND_HIT_VERIF', 'CND_HIT_ENTR', 'CONFIRMA', 'CND_HIT_REGIS_INV', 'CND_HIT_VERIF_INV', 'TPO_DOC_PRESENT', 'CONF_IMAGENES'], 'string', 'max' => 1],
            [['COD_MOTIVO_REPO', 'STAT_TRAM_TEMP'], 'string', 'max' => 2],
            #[['FEC_REGIS', 'FEC_CAPT_IMG', 'FEC_EMI_DUI', 'FEC_ENTR_DUI', 'FEC_PAGO', 'FEC_VENCE_DUI', 'FEC_RECTIFICA', 'FEC_ENTR_RECTIFICA', 'FEC_COMPLE', 'FEC_SUPERV_COMPLE', 'FEC_PAGO2', 'FEC_HORA_INICIO'], 'string', 'max' => 7],
            [['COD_OPER_REGIS', 'COD_OPER_IMG', 'COD_OPER_EMI_DUI', 'COD_OPER_ENTR_DUI', 'COD_CTRO_SERV', 'COD_SPRV_TRAM', 'COD_CTRO_QUEDAN', 'COD_OPER_COMPLE', 'COD_SUPERV_COMPLE', 'COD_OPER_RECTIFICA', 'COD_DEL_RECTIFICA', 'COD_OPER_REIMP', 'COD_DEL_REIMP', 'COD_DEL_CORREC', 'CALIDAD_IMAGEN', 'COD_CTRO_SERV_RECTI'], 'string', 'max' => 8],
            [['NUM_SOLIC'], 'string', 'max' => 9],
            [['COD_IMPR_EMI_DUI'], 'string', 'max' => 6],
            [['OBSERVACIONES'], 'string', 'max' => 50],
            [['SERIE_RECIBO1', 'SERIE_RECIBO2'], 'string', 'max' => 4],
            [['NUM_SOLIC'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'NUM_SOLIC' => 'Número Solicitud',
            'NUM_AFIS' => 'Num Afis',
            'NUDUI' => 'NUDUI',
            'NUM_FOLIO_DUI' => 'Num Folio DUI',
            'STAT_TRAM' => 'Estado',
            'TPO_TRAM' => 'Tipo Trámite',
            'COD_MOTIVO_REPO' => 'Cod Motivo Repo',
            'FEC_REGIS' => 'Fec Regis',
            'COD_OPER_REGIS' => 'Cod Oper Regis',
            'FEC_CAPT_IMG' => 'Fec Capt Img',
            'COD_OPER_IMG' => 'Cod Oper Img',
            'FEC_EMI_DUI' => 'Fec Emi Dui',
            'COD_OPER_EMI_DUI' => 'Cod Oper Emi Dui',
            'COD_IMPR_EMI_DUI' => 'Cod Impr Emi Dui',
            'FEC_ENTR_DUI' => 'Fec Entr Dui',
            'COD_OPER_ENTR_DUI' => 'Cod Oper Entr Dui',
            'COD_CTRO_SERV' => 'Cod Ctro Serv',
            'NUM_REC_PAGO' => 'Num Rec Pago',
            'FEC_PAGO' => 'Fec Pago',
            'COD_AGEN_BANCO' => 'Cod Agen Banco',
            'COD_BANCO' => 'Cod Banco',
            'PRECIO' => 'Precio',
            'COD_SPRV_TRAM' => 'Cod Sprv Tram',
            'STAT_AFIS_AUTORIZA' => 'Stat Afis Autoriza',
            'CONTEO_TRAM' => 'Conteo Tram',
            'ETAPA_TRAM' => 'Etapa Tram',
            'CND_HIT_REGIS' => 'Cnd Hit Regis',
            'CND_HIT_CAPT_IMG' => 'Cnd Hit Capt Img',
            'CND_HIT_VERIF' => 'Cnd Hit Verif',
            'CND_HIT_ENTR' => 'Cnd Hit Entr',
            'FEC_VENCE_DUI' => 'Fecha Vencimiento DUI',
            'OBSERVACIONES' => 'Observaciones',
            'STAT_TRAM_TEMP' => 'Stat Tram Temp',
            'FEC_RECTIFICA' => 'Fec Rectifica',
            'CONFIRMA' => 'Confirma',
            'NUM_SOLIC_INV' => 'Num Solic Inv',
            'NUM_AFIS_INV' => 'Num Afis Inv',
            'CND_HIT_REGIS_INV' => 'Cnd Hit Regis Inv',
            'CND_HIT_VERIF_INV' => 'Cnd Hit Verif Inv',
            'COD_CTRO_QUEDAN' => 'Cod Ctro Quedan',
            'TPO_DOC_PRESENT' => 'Tpo Doc Present',
            'CONF_IMAGENES' => 'Conf Imagenes',
            'FEC_ENTR_RECTIFICA' => 'Fec Entr Rectifica',
            'COD_OPER_COMPLE' => 'Cod Oper Comple',
            'COD_SUPERV_COMPLE' => 'Cod Superv Comple',
            'FEC_COMPLE' => 'Fec Comple',
            'FEC_SUPERV_COMPLE' => 'Fec Superv Comple',
            'PART_MARCADA' => 'Part Marcada',
            'COD_OPER_RECTIFICA' => 'Cod Oper Rectifica',
            'COD_DEL_RECTIFICA' => 'Cod Del Rectifica',
            'COD_OPER_REIMP' => 'Cod Oper Reimp',
            'COD_DEL_REIMP' => 'Cod Del Reimp',
            'COD_DEL_CORREC' => 'Cod Del Correc',
            'CALIDAD_IMAGEN' => 'Calidad Imagen',
            'NUM_REC_PAGO2' => 'Num Rec Pago2',
            'FEC_PAGO2' => 'Fec Pago2',
            'COD_AGEN_BANCO2' => 'Cod Agen Banco2',
            'COD_BANCO2' => 'Cod Banco2',
            'PRECIO2' => 'Precio2',
            'COD_CTRO_SERV_RECTI' => 'Cod Ctro Serv Recti',
            'SERIE_RECIBO1' => 'Serie Recibo1',
            'SERIE_RECIBO2' => 'Serie Recibo2',
            'FEC_HORA_INICIO' => 'Fec Hora Inicio',
            'dui'=> 'DUI',
            'firstNames'=> 'Nombres',
            'lastNames'=> 'Apellidos',
            'Application_Id'=> 'Aplicación',
            'documentNumber'=> 'Número de Folio',
        ];
    }
    
    public function afterFind() {
        if($this->FEC_VENCE_DUI){
            #$this->FEC_VENCE_DUI = Yii::$app->formatter->asDate($this->FEC_VENCE_DUI, 'php:d-m-Y');
        }
        if($this->tramvigentesdms){
            $this->dui = $this->tramvigentesdms->DUI;
            $this->firstNames = $this->tramvigentesdms->FIRSTNAMES;
            $this->lastNames = $this->tramvigentesdms->LASTNAMES;
            $this->Application_Id = $this->tramvigentesdms->APPLICATION_ID;
            $this->documentNumber = $this->tramvigentesdms->DOCUMENT_NUMBER;
        }
        return parent::afterFind();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTramvigentesdms()
    {
        return $this->hasOne(Tramvigentesdms::className(), ['NUM_SOLIC' => 'NUM_SOLIC']);
    }
    
    public  function getLastTram($criteria = []){
        try {
            $response = [];
            if(!empty($criteria)){
                if(isset($criteria['NUM_SOLIC'])){
                    $criteria["TRAM_VIGENTE.NUM_SOLIC"] = $criteria["NUM_SOLIC"];
                    unset($criteria["NUM_SOLIC"]);
                }
                $model = self::find()
                        ->innerJoin('TRAM_VIGENTE_SDMS', 'TRAM_VIGENTE_SDMS.NUM_SOLIC = TRAM_VIGENTE.NUM_SOLIC')
                        ->where($criteria)
                        ->andWhere("TRAM_VIGENTE_SDMS.STATE != 'LocallyDenied'")
                        ->orderBy('TRAM_VIGENTE.NUM_SOLIC DESC, TRAM_VIGENTE_SDMS.APPLICATION_ID DESC')
                        ->one();
                if(!empty($model)){
                    $response = array_merge($model->getAttributes(), 
                            ['dui'=> $model->dui
                            ,'firstNames'=> $model->firstNames
                            , 'lastNames'=> $model->lastNames
                            , 'Application_Id'=> $model->Application_Id
                            , 'documentNumber'=> $model->documentNumber
                            ]);
                }
            } 
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
