<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "userpreferences".
 *
 * @property int $IdUser
 * @property int $IdSettingDetail
 * @property string $Value
 *
 * @property Settingdetail $idSettingDetail
 * @property User $idUser
 */
class Userpreference extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'userpreference';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdUser', 'IdSettingDetail'], 'required'],
            [['IdUser', 'IdSettingDetail'], 'integer'],
            [['Value'], 'string', 'max' => 30],
            [['IdUser', 'IdSettingDetail'], 'unique', 'targetAttribute' => ['IdUser', 'IdSettingDetail']],
            [['IdSettingDetail'], 'exist', 'skipOnError' => true, 'targetClass' => Settingdetail::class, 'targetAttribute' => ['IdSettingDetail' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'IdUser' => 'Id User',
            'IdSettingDetail' => 'Id Setting Detail',
            'Value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSettingDetail()
    {
        return $this->hasOne(Settingdetail::class, ['Id' => 'IdSettingDetail']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUser']);
    }
}
