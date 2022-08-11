<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "visitor".
 *
 * @property int $Id
 * @property int $IdPerson
 * @property int $Code
 * @property int $IdState
 *
 * @property Person $person
 * @property State $state
 */
class Visitor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visitor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPerson', 'Code', 'IdState'], 'required'],
            [['IdPerson', 'Code', 'IdState'], 'integer'],
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
