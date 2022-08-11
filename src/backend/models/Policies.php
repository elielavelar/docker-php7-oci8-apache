<?php

namespace backend\models;

use Yii;
use common\models\State;
use common\models\Type;
use common\models\User;
use backend\models\Process;
use backend\models\CustomActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
/**
 * This is the model class for table "policies".
 *
 * @property int $Id
 * @property string $Name
 * @property string $Code
 * @property int $IdProcess
 * @property int $IdType
 * @property int $IdState
 * @property string $Description
 * @property int $IdUser
 *
 * @property State $state
 * @property Type $type
 * @property User $user
 * @property Process $process
 * @property Policyversions[] $policyversions 
 */
class Policies extends CustomActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'policies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name','Code' ,'IdProcess', 'IdType', 'IdState', 'IdUser'], 'required'],
            [['IdProcess', 'IdType', 'IdState', 'IdUser'], 'integer'],
            [['Code'], 'string','max' => 50],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 500],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdType' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'Id']],
            [['IdProcess'], 'exist', 'skipOnError' => true, 'targetClass' => Process::className(), 'targetAttribute' => ['IdProcess' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'Code' => 'Código',
            'IdProcess' => 'Proceso',
            'IdType' => 'Tipo',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
            'IdUser' => 'Usuario',
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
        $droptions = State::findAll(['KeyWord' => StringHelper::basename(self::class),]);
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdType']);
    }
    
    public function getTypes(){
        $droptions = Type::find()
                ->joinWith('state b')
                ->where([
                    'type.KeyWord' => StringHelper::basename(Policies::class),
                    'b.KeyWord' => StringHelper::basename(Type::class),
                    'b.Code' => Type::STATUS_ACTIVE,
                ])->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcess()
    {
        return $this->hasOne(Process::className(), ['Id' => 'IdProcess']);
    }
    
    public function getProcesses(){
        $droptions = Process::find()
            ->joinWith('state b')
            ->where([
                'b.KeyWord' => StringHelper::basename(Process::class),
                'b.Code' => Process::STATE_ACTIVE,
            ])->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdUser']);
    }
    
    /** 
     * @return \yii\db\ActiveQuery 
     */ 
    public function getPolicyversions() 
    { 
        return $this->hasMany(Policyversions::className(), ['IdPolicy' => 'Id']); 
    } 
}
