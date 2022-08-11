<?php

namespace backend\models;

use Yii;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use backend\models\Incidentcategory;
use common\models\State;

/**
 * This is the model class for table "incidentcategoryversion".
 *
 * @property int $Id
 * @property string $Name
 * @property string $Version
 * @property string $DateStart
 * @property string $DateEnd
 * @property int $CurrentVersion
 * @property int $IdState
 * @property string $Description
 *
 * @property Incidentcategory[] $incidentcategories
 * @property State $state
 */
class Incidentcategoryversion extends \yii\db\ActiveRecord
{
    
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    
    const CURRENT_ACTIVE = 1;
    const CURRENT_INACTIVE = 0;
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incidentcategoryversion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name','Version', 'IdState','DateStart'], 'required'],
            [['IdState'], 'integer'],
            [['Description'], 'string'],
            [['DateStart','DateEnd'], 'safe'],
            [['Version'], 'string', 'max' => 10],
            [['Version'], 'unique', 'targetAttribute' => ['Version']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['CurrentVersion'],'in','range' => [self::CURRENT_INACTIVE, self::CURRENT_ACTIVE]],
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
            'Version' => 'Versión',
            'DateStart' => 'Fecha Inicio',
            'DateEnd' => 'Fecha Fin',
            'IdState' => 'Estado',
            'CurrentVersion' => 'Versión Actual',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncidentcategories()
    {
        return $this->hasMany(Incidentcategory::className(), ['IdIncidentCategoryVersion' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $droptions = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }
    
    public function afterFind() {
        $this->DateStart = !empty($this->DateStart) ? \Yii::$app->getFormatter()->asDate($this->DateStart, 'php:d-m-Y') : $this->DateStart;
        $this->DateEnd = !empty($this->DateEnd) ? \Yii::$app->getFormatter()->asDate($this->DateEnd, 'php:d-m-Y') : $this->DateEnd;
        return parent::afterFind();
    }
}
