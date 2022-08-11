<?php

namespace console\models;

use Yii;

/**
 * This is the model class for table "citizen".
 *
 * @property integer $Id
 * @property string $Name
 * @property string $LastName
 * @property string $Email
 * @property string $Telephone
 * @property string $PasswordHash
 * @property string $PasswordResetToken
 * @property string $AuthKey
 * @property string $CreateDate
 * @property string $UpdateDate
 * @property integer $IdState
 *
 * @property State $idState
 */
class Citizen extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'citizen';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'LastName', 'PasswordHash', 'AuthKey', 'IdState'], 'required'],
            [['CreateDate', 'UpdateDate'], 'safe'],
            [['IdState'], 'integer'],
            [['Name', 'Email'], 'string', 'max' => 50],
            [['LastName'], 'string', 'max' => 65],
            [['Telephone'], 'string', 'max' => 12],
            [['PasswordHash', 'PasswordResetToken', 'AuthKey'], 'string', 'max' => 100],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Name',
            'LastName' => 'Last Name',
            'Email' => 'Email',
            'Telephone' => 'Telephone',
            'PasswordHash' => 'Password Hash',
            'PasswordResetToken' => 'Password Reset Token',
            'AuthKey' => 'Auth Key',
            'CreateDate' => 'Create Date',
            'UpdateDate' => 'Update Date',
            'IdState' => 'Id State',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
}
