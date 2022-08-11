<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "modelfields".
 *
 * @property int $Id
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property int $IdRecord
 * @property int $IdState
 * @property string $Description
 *
 * @property Modelfielddetail[] $modelfielddetails
 * @property State $state
 */
class Modelfield extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'modelfield';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'KeyWord', 'Code', 'IdRecord', 'IdState'], 'required'],
            [['IdRecord', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['Name', 'KeyWord', 'Code'], 'string', 'max' => 100],
            [['KeyWord', 'Code', 'IdRecord'], 'unique', 'targetAttribute' => ['KeyWord', 'Code', 'IdRecord']],
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
            'Name' => 'Name',
            'KeyWord' => 'Key Word',
            'Code' => 'Code',
            'IdRecord' => 'Id Record',
            'IdState' => 'Id State',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelfielddetails()
    {
        return $this->hasMany(Modelfielddetail::class, ['IdModelField' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
}
