<?php
namespace backend\models\sdms;

use Yii;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\db\Expression;
use Exception;
use backend\models\sdms\CtroServ;
use backend\models\sdms\CatCargoOper;
use backend\models\sdms\RolOperSistema;
use backend\models\Settingsdetail;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use kartik\password\StrengthValidator;

/**
 * This is the model class for table "DATOS_OPER".
 *
 * @property string $COD_OPER
 * @property string $PASWD_SISTEMA write-only password
 * @property string $PASWD_RED
 * @property string $NOM1_OPER
 * @property string $NOM2_OPER
 * @property string $NOM3_OPER
 * @property string $APDO1_OPER
 * @property string $APDO2_OPER
 * @property int $COD_ROL
 * @property int $COD_CARGO_OPER
 * @property string $STAT_OPER
 * @property int $COD_CTRO_SERV
 * @property int $COD_EMPLEADO
 * @property string $FECHA_CAMBIO
 * 
 * @property CtroServ $ctroServ
 * @property CatCargoOper $cargoOper
 * @property RolOperSistema $codRol
 */

class DatosOper extends ActiveRecord {
    
    const STATUS_ACTIVE = 'A';
    const STATUS_INACTIVE = 'I';
    const COD_PAIS_DEFAULT = 'SV';
    const COD_CHIEF = 2;
    const COD_CHIEF_ASSISTANT = 11;
    const COD_OFFICER = 10;
    
    public $NOM_STAT;
    public $nameOper = NULL;
    private $codeSettings = [];
    const SETTING_CODOPER = 'CODOPER';
    const DEFAULT_TYPE_CODE = 'OTHER';
    const DEFAULT_CODE = 'C';
    
    private $_customPassword = FALSE;
    public $_password = NULL;
    public $_passwordconfirm = NULL;
    
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.DATOS_OPER';
    }
    
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'FECHA_CAMBIO',
                ],
                'value' => function ($event) {
                    return new Expression("TO_DATE('" . $this->FECHA_CAMBIO . "','DD-MM-YYYY HH24:MI:SS')");
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
            [['COD_OPER', 'NOM1_OPER', 'APDO1_OPER', 'COD_ROL', 'COD_CTRO_SERV', 'COD_CARGO_OPER'], 'required','on'=> 'default'],
            [['COD_OPER', 'NOM1_OPER', 'APDO1_OPER', 'PASWD_SISTEMA', 'COD_ROL', 'COD_CTRO_SERV', 'COD_CARGO_OPER'], 'required' ,'on' => 'create'],
            [['COD_ROL','COD_CARGO_OPER','COD_CTRO_SERV'], 'integer'],
            [['COD_OPER','COD_EMPLEADO'], 'string', 'max' => 8],
            [['STAT_OPER'], 'string', 'max' => 1],
            [['COD_OPER'], 'string', 'min' => 3],
            [['PASWD_SISTEMA','PASWD_RED'], 'string', 'max' => 15],
            [['COD_OPER','COD_EMPLEADO'], 'unique'],
            ['STAT_OPER','default','value'=> self::STATUS_ACTIVE],
            [['STAT_OPER'],'in','range'=>[self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['COD_CTRO_SERV'], 'exist', 'skipOnError' => true, 'targetClass' => CtroServ::className(), 'targetAttribute' => ['COD_CTRO_SERV' => 'COD_CTRO_SERV']],
            [['COD_CARGO_OPER'], 'exist', 'skipOnError' => true, 'targetClass' => CatCargoOper::className(), 'targetAttribute' => ['COD_CARGO_OPER' => 'COD_CARGO_OPER']],
            [['COD_ROL'], 'exist', 'skipOnError' => true, 'targetClass' => RolOperSistema::className(), 'targetAttribute' => ['COD_ROL' => 'COD_ROL']],
            ['_passwordconfirm', 'string', 'min' => 8],
            ['_password', StrengthValidator::className(),'preset'=>'normal','userAttribute'=>'COD_OPER'],
            ['_passwordconfirm', 'compare', 'compareAttribute'=>'_password', 'message'=>"Contraseñas no coinciden" ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_OPER' => 'Código Operador',
            'NOM1_OPER' => 'Primer Nombre',
            'NOM2_OPER' => 'Segundo Nombre',
            'NOM3_OPER' => 'Tercer Nombre',
            'APDO1_OPER' => 'Primer Apellido',
            'APDO2_OPER' => 'Segundo Apellido',
            'COD_ROL' => 'Rol',
            'COD_CARGO_OPER' => 'Cargo',
            'COD_CTRO_SERV' => 'Centro de Servicio',
            'STAT_OPER' => 'Estado',
            'COD_EMPLEADO' => 'Cód. Empleado',
            'FECHA_CAMBIO' => 'Fecha Cambio',
            'PASSWD_SISTEMA' => 'Contraseña Sistema',
            'PASSWD_RED' => 'Contraseña Red',
            'nameOper' => 'Nombre',
            '_password' => 'Contraseña',
            '_passwordconfirm' => 'Confirmar Contraseña',
        ];
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->PASWD_SISTEMA = $password;
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCtroServ()
    {
        return $this->hasOne(CtroServ::className(), ['COD_CTRO_SERV' => 'COD_CTRO_SERV']);
    }
    
    public function getCtroServs(){
        try {
            $ctros = CtroServ::find()
                    ->where(['COD_PAIS' => self::COD_PAIS_DEFAULT])
                    ->andWhere('TEL_CTRO_SERV IS NOT NULL')
                    ->orderBy('TO_NUMBER(COD_CTRO_SERV) ASC')
                    ->all();
            return ArrayHelper::map($ctros, 'COD_CTRO_SERV', 'DESC_CTRO_SERV');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCargoOper()
    {
        return $this->hasOne(CatCargoOper::className(), ['COD_CARGO_OPER' => 'COD_CARGO_OPER']);
    }
    
    public function getCargosOper(){
        try {
            $data = CatCargoOper::find()->all();
            return ArrayHelper::map($data, 'COD_CARGO_OPER', 'DESC_CARGO_OPER');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCodRol()
    {
        return $this->hasOne(RolOperSistema::className(), ['COD_ROL' => 'COD_ROL']);
    }
    
    public function getCodRols(){
        try {
            $data = RolOperSistema::find()
                    ->orderBy(['COD_ROL' => SORT_ASC])
                    ->all();
            return ArrayHelper::map($data, 'COD_ROL', 'DESCRIPCION');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function afterFind() {
        #$this->FECHA_CAMBIO = !empty($this->FECHA_CAMBIO) ? Yii::$app->formatter->asDatetime($this->FECHA_CAMBIO, 'php:d-m-Y H:i:s') : $this->FECHA_CAMBIO;
        $this->NOM_STAT = $this->STAT_OPER == self::STATUS_ACTIVE ? 'ACTIVO':'INACTIVO';
        $this->nameOper = $this->NOM1_OPER." ".$this->APDO1_OPER;
        $this->_password = $this->PASWD_SISTEMA;
        return parent::afterFind();
    }
    
    public function getCode(){
        try {
            $cod = [];
            $code = NULL;
            if(!empty($this->COD_CARGO_OPER)) {
                $this->_getCodeSettings();
                if(isset($this->codeSettings[$this->COD_CARGO_OPER])){
                    array_push($cod, $this->codeSettings[$this->COD_CARGO_OPER]);
                } elseif(isset($this->codeSettings[self::DEFAULT_CODE])) {
                    array_push($cod, $this->codeSettings[self::DEFAULT_TYPE_CODE]);
                } else {
                    array_push($cod, self::DEFAULT_CODE);
                }
                if(!empty($this->COD_EMPLEADO)){
                    array_push($cod, $this->COD_EMPLEADO);
                    $code = implode('', $cod);
                } else {
                    $nm = strtoupper(substr($this->NOM1_OPER, 0,1));
                    $ap = strtoupper(substr($this->APDO1_OPER, 0,1));

                    if(!empty($this->COD_CTRO_SERV)){
                        $ctro = str_repeat('0', 2-strlen($this->COD_CTRO_SERV)).$this->COD_CTRO_SERV;
                        array_push($cod, $ctro);
                    }
                    array_push($cod, $nm);
                    array_push($cod, $ap);
                    $code = implode('', $cod);
                }
            }
            $this->COD_OPER = strtoupper($code);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _getCodeSettings(){
        try {
            $settings = Settingsdetail::find()
                        ->joinWith('setting b', FALSE)
                        ->where(['b.KeyWord' => StringHelper::basename(self::class)
                                ,'b.Code' => self::SETTING_CODOPER
                            ])->all();

            foreach ($settings as $set){
                $this->codeSettings[$set->Code] = $set->Value;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function beforeSave($insert) {
        try {
            if($this->isNewRecord){
                $this->_password = Yii::$app->security->generateRandomString(7).random_int(0, 9);
            }
            if($this->_password){
                $this->setPassword($this->_password);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function getCompleteName(){
        try {
            if($this->isNewRecord){
                throw new Exception('Datos de operador no cargados', 90000);
            }
            return $this->NOM1_OPER.(!empty($this->NOM2_OPER) ? ' '.$this->NOM2_OPER:'')
                .(!empty($this->NOM3_OPER) ? ' '.$this->NOM3_OPER:'')
                .' '.$this->APDO1_OPER
                .(!empty($this->APDO2_OPER) ? ' '.$this->APDO2_OPER:'')
                ;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
}
