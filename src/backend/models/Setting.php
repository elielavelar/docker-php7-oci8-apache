<?php

namespace backend\models;

use Yii;
use common\models\State;
use common\models\Type;
use common\models\Company;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\CustomActiveRecord;

/**
 * This is the model class for table "setting".
 *
 * @property integer $Id
 * @property integer $IdCompany
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property integer $IdState
 * @property integer $IdType
 * @property string $Description
 *
 * @property State $state
 * @property Type $type
 * @property Company $company
 * @property Settingdetail[] $settingdetails
 */
class Setting extends CustomActiveRecord
{
    
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'setting';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'Code', 'IdCompany', 'IdState', 'IdType'], 'required'],
            [['IdState', 'IdType', 'IdCompany'], 'integer'],
            [['Description'], 'string'],
            [['Name', 'KeyWord'], 'string', 'max' => 50],
            [['Code'], 'string', 'max' => 20],
            [['IdCompany'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['IdCompany' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
            [['Code'], 'unique', 'targetAttribute' => ['IdCompany','KeyWord','Code'], 'message' => 'Ya existe el código {value} en los parámetros'],
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
            'KeyWord' => 'Llave',
            'Code' => 'Código',
            'IdState' => 'Estado',
            'IdType' => 'Tipo',
            'Description' => 'Descripción',
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
        try {
            return ArrayHelper::map(State::findAll(['KeyWord'=>StringHelper::basename(self::class)]), 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
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
            return ArrayHelper::map(Type::findAll(['KeyWord'=>StringHelper::basename(self::class)]), 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSettingdetails()
    {
        return $this->hasMany(Settingdetail::class, ['IdSetting' => 'Id']);
    }
}
