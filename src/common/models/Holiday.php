<?php

namespace common\models;

use Yii;
use common\models\Type;
use common\models\State;
use \yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use nepstor\validators\DateTimeCompareValidator;
use Exception;

/**
 * This is the model class for table "holidays".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $Description
 * @property integer $IdType
 * @property integer $IdState
 * @property string $DateStart
 * @property string $DateEnd
 * @property integer $IdFrequencyType
 *
 * @property Type $type
 * @property State $state
 * @property Type $frequencyType
 * @property Holidaydetail[] $holidaydetails
 */
class Holiday extends \yii\db\ActiveRecord
{
    
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    
    const TYPE_GENERAL = 'GNRL';
    const TYPE_SPECIFIC = 'SPCF';
    
    const TYPE_FREQ_ANNUAL = 'YEAR';
    const TYPE_FREQ_SPECIAL = 'SPCL';
    
    private $date_format = 'd-m-Y';
    public $holidaysitems = [];
    
    public $create = FALSE;
    public $update = FALSE;
    public $delete = FALSE;
    public $view = FALSE;
    public $details = FALSE;
    
    
    private $_controller;
    private $_controllerName = 'holiday';
    private $_loadByController = FALSE;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'holiday';
    }
    
    public function __construct($config = array()) {
        parent::__construct($config);
        $this->_controller = \Yii::$app->controller;
        if(!empty($this->_controller)){
            $this->_loadByController = $this->_controller->id == $this->_controllerName;
        }
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'IdType', 'IdState', 'DateStart', 'DateEnd', 'IdFrequencyType'], 'required'],
            [['Description'], 'string'],
            [['IdType', 'IdState', 'IdFrequencyType'], 'integer'],
            [['DateStart', 'DateEnd'], 'safe'],
            [['Name'], 'string', 'max' => 50],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdFrequencyType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdFrequencyType' => 'Id']],
            [['DateEnd'],  DateTimeCompareValidator::class,'compareAttribute'=> 'DateStart'
                ,'operator'=>'>=','format'=> $this->date_format,'jsFormat'=>'DD-MM-YYYY'
                ,'message'=>'{attribute} debe ser mayor o igual que {compareAttribute}'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'Description' => 'Descripción',
            'IdType' => 'Tipo',
            'IdState' => 'Estado',
            'DateStart' => 'Fecha Inicio',
            'DateEnd' => 'Fecha Fin',
            'IdFrequencyType' => 'Tipo de Frecuencia',
            'holidaysitems' => 'Duicentros',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }
    
    public function getTypes(){
        try {
            $droptions = Type::findAll(['KeyWord'=> StringHelper::basename(self::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
    
    public function getStates(){
        try {
            $droptions = State::findAll(['KeyWord'=> StringHelper::basename(self::class)]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFrequencyType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdFrequencyType']);
    }
    
    public function getFrequencyTypes(){
        try {
            $droptions = Type::findAll(['KeyWord'=> StringHelper::basename(self::class)."Frequency"]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHolidaysdetails()
    {
        return $this->hasMany(Holidaydetail::class, ['IdHoliday' => 'Id']);
    }
    
    
    public function afterFind() {
        if($this->_loadByController){
            $this->create = \Yii::$app->user->can($this->_controllerName."Create");
            $this->update = \Yii::$app->user->can($this->_controllerName."Update");
            $this->delete = \Yii::$app->user->can($this->_controllerName."Delete");
            $this->view = \Yii::$app->user->can($this->_controllerName."View");
            
            $this->details = $this->IdType ? ($this->type->Code != self::TYPE_GENERAL):FALSE;
        }
        if($this->DateStart){
            $this->DateStart = \Yii::$app->formatter->asDate($this->DateStart, "php:$this->date_format");
        }
        if($this->DateEnd){
            $this->DateEnd = \Yii::$app->formatter->asDate($this->DateEnd, "php:$this->date_format");
        }
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        if(parent::beforeSave($insert)){
            $this->DateStart = Yii::$app->formatter->asDate($this->DateStart, 'php:Y-m-d');
            $this->DateEnd = \Yii::$app->formatter->asDate($this->DateEnd,'php:Y-m-d');
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        $this->DateStart = \Yii::$app->formatter->asDate($this->DateStart, 'php:d-m-Y');
        $this->DateEnd = \Yii::$app->formatter->asDate($this->DateEnd, 'php:d-m-Y');
        
        $this->_saveDetails();
        
        return parent::afterSave($insert, $changedAttributes);
    }
    
    private function _saveDetails(){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            if($this->Id != NULL) {
                Holidaydetail::deleteAll(['IdHoliday'=> $this->Id]);
            }
            if(!empty($this->holidaysitems)){
                $state = State::findOne(['KeyWord'=> StringHelper::basename(self::class),'Code'=> self::STATUS_ACTIVE])->Id;
                $details = [];
                foreach ($this->holidaysitems as $key){
                    $details = [
                        'IdHoliday'=> $this->Id,
                        'IdServiceCentre' => $key,
                        'IdState' => $state,
                    ];
                    $det = new Holidaydetail();
                    $det->attributes = $details;
                    if(!$det->save()){
                        throw new Exception(\Yii::$app->customFunctions->getErrors($det->errors),92000);
                    } 
                }
            }
            $transaction->commit();
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        
    }
}
