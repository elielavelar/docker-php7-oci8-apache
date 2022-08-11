<?php

namespace backend\models;

use Yii;
use common\models\State;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use backend\models\Servicecentreservices;
use Exception;

/**
 * This is the model class for table "servicetaskcustomstates".
 *
 * @property int $Id
 * @property int $IdServiceTask
 * @property int $IdState
 * @property string $DateStart
 * @property string $DateEnd
 * @property int $Active
 * @property int $IdUserCreate
 * @property int $IdUserDisabled
 * @property string $Description
 *
 * @property Servicetask $serviceTask
 * @property State $state
 * @property User $userCreate
 * @property User $userDisabled
 */
class Servicetaskcustomstates extends \yii\db\ActiveRecord
{
    const _STATE_ACTIVE_ = 1;
    const _STATE_INACTIVE_ = 0;
    public $userCreateName = null;
    public $userDisableName = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicetaskcustomstates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdServiceTask', 'IdState', 'IdUserCreate', 'Description'], 'required'],
            [['IdServiceTask', 'IdState', 'IdUserCreate', 'IdUserDisabled','Active'], 'integer'],
            [['DateStart', 'DateEnd'], 'safe'],
            [['Description'], 'string'],
            [['IdServiceTask'], 'exist', 'skipOnError' => true, 'targetClass' => Servicetask::className(), 'targetAttribute' => ['IdServiceTask' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdUserCreate'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUserCreate' => 'Id']],
            [['IdUserDisabled'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUserDisabled' => 'Id']],
            [['Active'],'in','range'=>[self::_STATE_ACTIVE_, self::_STATE_INACTIVE_]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdServiceTask' => 'Tarea',
            'IdState' => 'Estado Servicio',
            'DateStart' => 'Fecha Inicio',
            'DateEnd' => 'Fecha Fin',
            'Active' => 'Activo',
            'IdUserCreate' => 'Usuario Creación',
            'IdUserDisabled' => 'Usuario Cierre',
            'Description' => 'Descripción',
            'userCreateName' => 'Usuario Creación',
            'userDisableName' => 'Usuario Cierre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceTask()
    {
        return $this->hasOne(Servicetask::className(), ['Id' => 'IdServiceTask']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }

    public function getStates(){
        $options = State::findAll(['KeyWord' => StringHelper::basename(Servicecentreservice::class)]);
        return ArrayHelper::map($options, 'Id','Name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreate()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdUserCreate']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserDisabled()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdUserDisabled']);
    }
    
    public function afterFind() {
        $this->userCreateName = $this->IdUserCreate ? $this->userCreate->DisplayName: null;
        $this->userDisableName = $this->IdUserDisabled ? $this->userDisabled->DisplayName: null;
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        if($this->isNewRecord){
            $this->IdUserCreate = Yii::$app->getUser()->getIdentity()->getId();
        } else {
            if($this->Active == 0 && $this->oldAttributes['Active'] == 1){
                $this->IdUserDisabled = Yii::$app->getUser()->getIdentity()->getId();
            }
        }
        return parent::beforeSave($insert);
    }
}
