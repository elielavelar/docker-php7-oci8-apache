<?php

namespace common\models;

use Yii;
use common\models\Zone;
use common\models\State;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "zonesupervisors".
 *
 * @property int $Id
 * @property int $IdZone
 * @property int $IdUser
 * @property int $IdState
 *
 * @property State $state
 * @property User $user
 * @property Zones $zone
 */
class Zonesupervisors extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zonesupervisor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdZone', 'IdUser', 'IdState'], 'required'],
            [['IdZone', 'IdUser', 'IdState'], 'integer'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdZone'], 'exist', 'skipOnError' => true, 'targetClass' => Zone::class, 'targetAttribute' => ['IdZone' => 'Id']],
            [['IdUser'], 'unique', 'targetAttribute' => ['IdZone', 'IdUser'], 'message' => 'Ya existe el Usuario {value} para la zona ingresada'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdZone' => 'Zona',
            'IdUser' => 'Usuario',
            'IdState' => 'Estado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $model = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($model, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUser']);
    }
    
    public function getUsers(){
        $model = User::find()
                ->joinWith('state b')
                ->joinWith('profile c')
                ->where([
                    'b.Code' => User::STATE_ACTIVE,
                    'c.Code' => User::PROFILE_SUPERVISOR,
                ])->all();
        return ArrayHelper::map($model, 'Id', 'DisplayName');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZone()
    {
        return $this->hasOne(Zone::class, ['Id' => 'IdZone']);
    }
}
