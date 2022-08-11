<?php

namespace backend\models;

use Yii;
use common\models\State;
use backend\models\Nullreasons;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "nullreasoncodes".
 *
 * @property int $Id
 * @property string $Name
 * @property int $IdNullReason
 * @property string $Code
 * @property int $IdState
 * @property string $CodDevol
 * @property string $Description
 *
 * @property Nullreasons $nullReason
 * @property State $state
 */
class Nullreasoncodes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nullreasoncodes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'IdNullReason', 'Code', 'IdState', 'CodDevol'], 'required'],
            [['IdNullReason', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['Code', 'CodDevol'], 'string', 'max' => 10],
            [['Code'], 'unique'],
            [['IdNullReason'], 'exist', 'skipOnError' => true, 'targetClass' => Nullreasons::className(), 'targetAttribute' => ['IdNullReason' => 'Id']],
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
            'IdNullReason' => 'Id Null Reason',
            'Code' => 'Code',
            'IdState' => 'Id State',
            'CodDevol' => 'Cod Devol',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNullReason()
    {
        return $this->hasOne(Nullreasons::className(), ['Id' => 'IdNullReason']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
}
