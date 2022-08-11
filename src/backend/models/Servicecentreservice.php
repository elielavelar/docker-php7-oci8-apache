<?php

namespace backend\models;

use Yii;
use common\models\Type;
use common\models\State;
use common\models\Servicecentre;
use backend\models\Servicetask;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "servicecentreservices".
 *
 * @property int $Id
 * @property int $IdServiceCentre
 * @property string $Name
 * @property string $Code
 * @property int $IdType
 * @property int $IdState
 * @property string $Description
 *
 * @property Servicecentres $serviceCentre
 * @property State $state
 * @property Type $type
 * @property Servicetask[] $servicetasks
 */
class Servicecentreservice extends \yii\db\ActiveRecord
{
    
    const DEFAULT_STATE = 'ACT';
    const STATE_ACTIVE =  'ACT';
    const STATE_INACTIVE =  'INA';
    const STATE_WARNING =  'WARN';
    const STATE_ERROR =  'ERR';
    public $css_class = NULL;
    public $messages = [];
    public $label_message= NULL;
    
    
    public function __construct($config = array()) {
        $this->messages = [
            self::STATE_ACTIVE => ['css' => 'bg-green status_success', 'label' => 'OK'],
            self::STATE_INACTIVE => ['css'=>'bg-gray status_inactive', 'label' => 'OFF'],
            self::STATE_WARNING =>['css'=>'bg-yellow status_warning', 'label' => 'WARN'],
            self::STATE_ERROR => ['css'=>'bg-red status_error', 'label' => 'ERR'],
        ];
        return parent::__construct($config);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicecentreservices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdServiceCentre', 'Name','Code', 'IdType', 'IdState'], 'required'],
            [['IdServiceCentre', 'IdType', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 20],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::class, 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdServiceCentre' => 'Centro de Servicio',
            'Name' => 'Nombre',
            'Code' => 'Código',
            'IdType' => 'Tipo',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::class, ['Id' => 'IdServiceCentre']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $data = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($data, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }
    
    public function getTypes(){
        $data = Type::find()
                ->joinWith('state b', false)
                ->where([
                    'type.KeyWord' => StringHelper::basename(self::class),
                    'b.KeyWord' => StringHelper::basename(Type::class),
                    'b.Code' => Type::STATUS_ACTIVE,
                ])
                ->orderBy(['type.Id' => SORT_ASC])
                ->all();
        return ArrayHelper::map($data, 'Id', 'Name');
    }
    
    public function afterFind() {
        try {
            $this->css_class = $this->IdState ? (isset($this->messages[$this->state->Code]) ? $this->messages[$this->state->Code]['css']:NULL):NULL;
            $this->label_message = $this->IdState ? (isset($this->messages[$this->state->Code]) ? $this->messages[$this->state->Code]['label']:NULL):NULL;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServicetasks()
    {
        return $this->hasMany(Servicetask::class, ['IdService' => 'Id']);
    }
    
    public function getServiceStatus(){
        try {
            $response = [];
            foreach ($this->servicetasks as $task){
                $response = $task->getServiceStatus();
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
