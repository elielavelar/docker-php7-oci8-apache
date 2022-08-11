<?php

namespace console\models;

use Yii;
use common\models\State;
use common\models\Type;
use common\models\Countries;
use common\models\Zones;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use backend\models\Servicecentreservices;
use Exception;

/**
 * This is the model class for table "servicecentres".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $ShortName
 * @property string $Code
 * @property integer $MBCode
 * @property integer $IdCountry
 * @property integer $IdZone
 * @property integer $IdState
 * @property integer $IdType
 * @property string $Address
 * @property string $Phone
 *
 * @property State $state
 * @property Zones $zone
 * @property Type $type
 * @property Countries $country
 * @property Appointmentservicesetting[] $appointmentservicesettings
 * @property Servicecentreservices[] $services
 */
class Servicecentres extends \yii\db\ActiveRecord {
    //put your code here
    use \common\models\traits\Servicecentretrait;
    const _FILE_PATH_ = '@console/temp/logs';
    const _PATH_ATTACHMENTS_ = 'logs';
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    
    const TYPE_DUISITE = 'DUISITE';
    const TYPE_OFFICE = 'OFFC';
    const TYPE_CONSULATE  = 'CONS';
    const TYPE_DATACENTER  = 'DATA';
    
    const SUGGESTION_CODE = 'SGST';
    
    const PROFILE_CHIEF_SERVICECENTRE_DUISITE = 'JFDC';
    const PROFILE_AUXCHIEF_SERVICECENTRE_DUISITE = 'AUXJF';
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'servicecentres';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'Code','MBCode','ShortName', 'IdCountry', 'IdState', 'IdType','IdZone'], 'required'],
            [['MBCode', 'IdCountry', 'IdState', 'IdType', 'IdZone'], 'integer'],
            [['Address'], 'string'],
            [['MBCode'],'unique','message' => 'Código MB {value} ya existe'],
            [['Code'],'unique','message' => 'Código {value} ya existe'],
            [['Name','ShortName'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 10],
            [['Phone'], 'string', 'max' => 15],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdType' => 'Id']],
            [['IdCountry'], 'exist', 'skipOnError' => true, 'targetClass' => Countries::className(), 'targetAttribute' => ['IdCountry' => 'Id']],
            [['IdZone'], 'exist', 'skipOnError' => true, 'targetClass' => Zones::className(), 'targetAttribute' => ['IdZone' => 'Id']],
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
            'ShortName' => 'Nombre Corto',
            'Code' => 'Código',
            'MBCode' => 'Código Muhlbauer',
            'IdCountry' => 'País',
            'IdZone' => 'Zona',
            'IdState' => 'Estado',
            'IdType' => 'Tipo',
            'Address' => 'Dirección',
            'Phone' => 'Teléfono',
            'nameCountry' => 'País',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        try {
            $droptions = State::findAll(['KeyWord'=>StringHelper::basename(self::className())]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdType']);
    }
    
    public function getTypes(){
        try {
            $droptions = Type::findAll(['KeyWord'=>StringHelper::basename(self::className())]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Countries::className(), ['Id' => 'IdCountry']);
    }
    
    public function getCountries(){
        try {
            $droptions = Countries::findAll(['IdState'=>  State::findOne(['KeyWord'=>'Countries','Code'=>'ACT'])]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zones::className(), ['Id' => 'IdZone']);
    }
    
    public function getZones(){
        try {
            $droptions = Zones::findAll(['IdState'=>  State::findOne(['KeyWord'=>  StringHelper::basename(Zones::className()),'Code'=>  Zones::STATUS_ACTIVE])]);
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
        
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Servicecentreservices::className(), ['IdServiceCentre' => 'Id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAppointmentservicesettings()
    {
        return $this->hasMany(Appointmentservicesetting::className(), ['IdServiceCentre' => 'Id']);
    }
    
    
    public function getServicesStatus(){
        try {
            $response = [];
            foreach ($this->services as $service){
                $response[$service->Code] = $service->getServiceStatus();
            }
            return ['success' => true,'values'=>[$this->Id => $response]];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getAllServicesStatus(){
        try {
            $response = [];
            $centres = self::find()
                    ->joinWith('state b')
                    ->joinWith('type c')
                    ->where([
                        'b.Code' => self::STATE_ACTIVE,
                    ])
                    ->andWhere('(c.Code IN(:duisite, :datacenter))',[':duisite' => self::TYPE_DUISITE,':datacenter' => self::TYPE_DATACENTER])
                    ->all();
            foreach ($centres as $centre){
                $result = [];
                foreach ($centre->services as  $service){
                    $result[$service->Code] = $service->getServiceStatus();
                }
                $response[$centre->Id] = $result;
            }
            return ['success' => true,'values'=>$response];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
