<?php

namespace backend\models;

use Yii;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nullreasons".
 *
 * @property int $Id
 * @property string $Name
 * @property int $Code
 * @property int $IdState
 *
 * @property Nullreasoncodes[] $nullreasoncodes
 * @property State $state
 */
class Nullreasons extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nullreasons';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Code', 'IdState'], 'required'],
            [['Code', 'IdState'], 'integer'],
            [['Name'], 'string', 'max' => 100],
            [['Code'], 'unique'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Name',
            'Code' => 'Code',
            'IdState' => 'Id State',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNullreasoncodes()
    {
        return $this->hasMany(Nullreasoncodes::className(), ['IdNullReason' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
}
