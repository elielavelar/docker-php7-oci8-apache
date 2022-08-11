<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "syslogdetail".
 *
 * @property int $Id
 * @property int $IdSysLog
 * @property string $Attribute
 * @property string $Value
 * @property string $OldValue
 *
 * @property Syslog $sysLog
 */
class Syslogdetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'syslogdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdSysLog', 'Attribute'], 'required'],
            [['IdSysLog'], 'integer'],
            [['Attribute'], 'string', 'max' => 200],
            [['Value', 'OldValue'], 'string'],
            [['IdSysLog'], 'exist', 'skipOnError' => true, 'targetClass' => Syslog::className(), 'targetAttribute' => ['IdSysLog' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdSysLog' => 'Id Sys Log',
            'Attribute' => 'Campo',
            'Value' => 'Valor',
            'OldValue' => 'Valor Anterior',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSysLog()
    {
        return $this->hasOne(Syslog::className(), ['Id' => 'IdSysLog']);
    }
}
