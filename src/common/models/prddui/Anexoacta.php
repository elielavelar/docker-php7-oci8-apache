<?php

namespace common\models\prddui;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\sdms\CtroServ;
use backend\models\sdms\DatosOper;
use Exception;


/**
 * This is the model class for table "ANEXO_ACTA".
 *
 * @property int $COD_CTRO_SERV
 * @property string $COD_JEFE
 * @property string $COD_DELEGADO
 * @property string $FEC_FACTURACION
 * @property string $FEC_ACTA
 * @property int $NUM_CORR_ACTA
 * @property int $PRIMERAVEZ
 * @property int $MODIFICACIONES
 * @property int $REPOSICIONES
 * @property int $RENOVACIONES
 * @property int $REIMPRESIONES
 * @property int $TAR_BASE_ANULADAS
 * @property int $TAR_DECAD_ANULADAS
 * 
 * @property CtroServ $ctroServ
 * @property DatosOper $chief
 * @property DatosOper $officer
 * 
 * @property Nullcard[] $nullcards
 */

class Anexoacta extends ActiveRecord {
    use Anexoactatrait;
    
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.ANEXO_ACTA';
    }
    
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'FEC_ACTA',
                ],
                'value' => function ($event) {
                    return new Expression("TO_DATE('" . $this->FEC_ACTA . "','YYYY-MM-DD HH24:MI:SS')");
                },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'FEC_FACTURACION',
                ],
                'value' => function ($event) {
                    return new Expression("TO_DATE('" . $this->FEC_FACTURACION . "','YYYY-MM-DD')");
                },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_VALIDATE => 'FEC_ACTA',
                ],
                'value' => function ($event) {
                    return !empty($this->FEC_ACTA) ? new Expression("TO_CHAR('" . $this->FEC_ACTA . "','DD-MM-YYYY HH24:MI:SS')") : null;
                },
            ],
            
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_CTRO_SERV', 'NUM_CORR_ACTA'], 'required'],
            [['COD_CTRO_SERV','NUM_CORR_ACTA','PRIMERAVEZ', 'MODIFICACIONES','REPOSICIONES','RENOVACIONES','REIMPRESIONES','TAR_BASE_ANULADAS','TAR_DECAD_ANULADAS'], 'integer'],
            [['FEC_FACTURACION', 'FEC_ACTA'], 'string'],
            [['COD_JEFE','COD_DELEGADO'], 'string', 'max' => 8],
            [['COD_CTRO_SERV','FEC_FACTURACION'], 'unique'],
            [['COD_CTRO_SERV'], 'exist', 'skipOnError' => true, 'targetClass' => CtroServ::class, 'targetAttribute' => ['COD_CTRO_SERV' => 'COD_CTRO_SERV']],
            [['COD_JEFE'], 'exist', 'skipOnError' => true, 'targetClass' => DatosOper::class, 'targetAttribute' => ['COD_JEFE' => 'COD_OPER']],
            [['COD_DELEGADO'], 'exist', 'skipOnError' => true, 'targetClass' => DatosOper::class, 'targetAttribute' => ['COD_DELEGADO' => 'COD_OPER']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_CTRO_SERV' => 'Duicentro',
            'COD_JEFE' => 'Jefe Duicentro',
            'COD_DELEGADO' => 'Delegado',
            'FEC_FACTURACION' => 'Fecha',
            'FEC_ACTA' => 'Fecha Acta',
            'NUM_CORR_ACTA' => 'Número Acta',
            'PRIMERAVEZ' => 'Primera Vez',
            'MODIFICACIONES' => 'Modificaciones',
            'REPOSICIONES' => 'Reposiciones',
            'RENOVACIONES' => 'Renovaciones',
            'TAR_BASE_ANULADAS' => 'Tarjetas Base Anuladas',
            'TAR_DECAD_ANULADAS' => 'Tarjetas Decad. Anuladas',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCtroServ()
    {
        return $this->hasOne(CtroServ::class, ['COD_CTRO_SERV' => 'COD_CTRO_SERV']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChief()
    {
        return $this->hasOne(DatosOper::class, ['COD_OPER' => 'COD_JEFE']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOfficer()
    {
        return $this->hasOne(DatosOper::class, ['COD_OPER' => 'COD_DELEGADO']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNullcards()
    {
        return $this->hasMany(Nullcard::class, ['COD_CTRO_SERV' => 'COD_CTRO_SERV', 'FECHA' => 'FEC_FACTURACION']);
    }
    
    public function afterFind() {
        if(!empty($this->FEC_FACTURACION)){
            $date = \DateTime::createFromFormat('d/m/Y H:i:s', $this->FEC_FACTURACION);
            $this->FEC_FACTURACION = $date->format('d-m-Y');
        }
        if(!empty($this->FEC_ACTA)){
            $date = \DateTime::createFromFormat('d/m/Y H:i:s', $this->FEC_ACTA);
            $this->FEC_ACTA = $date->format('d-m-Y H:i:s');
        }
        return parent::afterFind();
    }

   
}
