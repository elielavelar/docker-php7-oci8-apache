<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "holidaydetail".
 *
 * @property integer $Id
 * @property integer $IdHoliday
 * @property integer $IdServiceCentre
 * @property integer $IdState
 * @property integer $CustomDate
 * @property string $DateStart
 * @property string $DateEnd
 *
 * @property State $state
 * @property Holiday $holiday
 * @property Servicecentre $serviceCentre
 */
class Holidaydetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'holidaydetail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['IdHoliday', 'IdServiceCentre', 'IdState'], 'required'],
            [['IdHoliday', 'IdServiceCentre', 'IdState', 'CustomDate'], 'integer'],
            [['DateStart', 'DateEnd'], 'safe'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdHoliday'], 'exist', 'skipOnError' => true, 'targetClass' => Holiday::class, 'targetAttribute' => ['IdHoliday' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::class, 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['CustomDate'],'default','value'=>0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdHoliday' => 'Id Holiday',
            'IdServiceCentre' => 'Id Service Centre',
            'IdState' => 'Id State',
            'CustomDate' => 'Custom Date',
            'DateStart' => 'Date Start',
            'DateEnd' => 'Date End',
        ];
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
    public function getHoliday()
    {
        return $this->hasOne(Holiday::class, ['Id' => 'IdHoliday']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::class, ['Id' => 'IdServiceCentre']);
    }
}
