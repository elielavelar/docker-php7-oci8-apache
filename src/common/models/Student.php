<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "student".
 *
 * @property int $Id
 * @property int $IdPerson
 * @property string $Code
 * @property string|null $Email
 * @property string|null $AdmissionDate
 * @property string|null $GraduateDate
 * @property int $IdState
 *
 * @property Person $person
 * @property State $state
 */
class Student extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPerson', 'Code', 'IdState'], 'required'],
            [['IdPerson', 'IdState'], 'integer'],
            [['AdmissionDate', 'GraduateDate'], 'safe'],
            [['Code'], 'string', 'max' => 10],
            [['Email'], 'string', 'max' => 50],
            [['Code'], 'unique'],
            [['IdPerson'], 'unique'],
            [['IdPerson'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['IdPerson' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
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
            'Email' => 'Email',
            'AdmissionDate' => 'Admission Date',
            'GraduateDate' => 'Graduate Date',
            'IdState' => 'Id State',
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
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
}
