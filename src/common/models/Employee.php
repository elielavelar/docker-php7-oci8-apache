<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "employee".
 *
 * @property int $Id
 * @property int $IdPerson
 * @property string $Code
 * @property int $IdServiceCentre
 * @property string|null $Email
 * @property int $IdState
 * @property int $IdType
 * @property string $DateHiring
 * @property string|null $DateRetirement
 *
 * @property Person $person
 * @property Servicecentres $serviceCentre
 * @property State $state
 * @property Type $type
 */
class Employee extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employee';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPerson', 'Code', 'IdServiceCentre', 'IdState', 'IdType', 'DateHiring'], 'required'],
            [['IdPerson', 'IdServiceCentre', 'IdState', 'IdType'], 'integer'],
            [['DateHiring', 'DateRetirement'], 'safe'],
            [['Code'], 'string', 'max' => 10],
            [['Email'], 'string', 'max' => 50],
            [['Code'], 'unique'],
            [['IdPerson'], 'unique'],
            [['IdPerson'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['IdPerson' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentres::class, 'targetAttribute' => ['IdServiceCentre' => 'Id']],
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
            'IdPerson' => 'Id Person',
            'Code' => 'Code',
            'IdServiceCentre' => 'Id Service Centre',
            'Email' => 'Email',
            'IdState' => 'Id State',
            'IdType' => 'Id Type',
            'DateHiring' => 'Date Hiring',
            'DateRetirement' => 'Date Retirement',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::class, ['Id' => 'IdPerson']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentres::class, ['Id' => 'IdServiceCentre']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }
}
