<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "countrystate".
 *
 * @property int $Id
 * @property string $Name
 * @property int $IdCountry
 * @property int $Enabled
 *
 * @property City[] $cities
 * @property Country $country
 */
class Countrystate extends \yii\db\ActiveRecord
{
    use traits\Countrystatetrait;
    const STATUS_DISABLED = 0;
    const STATUS_ENABLED = 1;
    public $uploadFile = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'countrystate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'IdCountry'], 'required'],
            [['IdCountry', 'Enabled'], 'integer'],
            [['Name'], 'string', 'max' => 100],
            [['IdCountry', 'Name'], 'unique', 'targetAttribute' => ['IdCountry', 'Name']],
            [['IdCountry'], 'exist', 'skipOnError' => true, 'targetClass' => Country::class, 'targetAttribute' => ['IdCountry' => 'Id']],
            ['Enabled','default','value' => self::STATUS_ENABLED],
            ['Enabled','in','range' =>[self::STATUS_DISABLED, self::STATUS_ENABLED] ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('system', 'ID'),
            'Name' => Yii::t('system', 'Name'),
            'IdCountry' => Yii::t('system', 'IdCountry'),
            'Enabled' => Yii::t('system', 'Enabled'),
            'uploadFile' => Yii::t('system', 'uploadFile'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCities()
    {
        return $this->hasMany(City::class, ['IdCountryState' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Country::class, ['Id' => 'IdCountry']);
    }
    
    public function getStates(){
        return [ self::STATUS_DISABLED => Yii::t('app', 'No'), self::STATUS_ENABLED => Yii::t('app', 'Yes')];
    }
}
