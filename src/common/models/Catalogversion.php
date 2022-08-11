<?php

namespace common\models;

use Yii;
use common\models\CustomActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "catalogversions".
 *
 * @property int $Id
 * @property int $IdCatalog
 * @property string $Version
 * @property int $CurrentVersion
 * @property int $IdState
 * @property string $Description
 *
 * @property Catalogdetail[] $catalogdetails
 * @property Catalog $catalog
 * @property State $state
 */
class Catalogversion extends CustomActiveRecord
{

    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    const CURRENT_VERSION_ENABLED = 1;
    const CURRENT_VERSION_DISABLED = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalogversion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdCatalog', 'IdState'], 'required'],
            [['IdCatalog', 'IdState'], 'integer'],
            [['Description'], 'string'],
            [['Version'], 'string', 'max' => 10],
            [['IdCatalog', 'Version'], 'unique', 'targetAttribute' => ['IdCatalog', 'Version']],
            [['IdCatalog'], 'exist', 'skipOnError' => true, 'targetClass' => Catalog::class, 'targetAttribute' => ['IdCatalog' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            ['CurrentVersion','in','range' => [self::CURRENT_VERSION_DISABLED, self::CURRENT_VERSION_ENABLED]],
            ['CurrentVersion','default','value' => self::CURRENT_VERSION_DISABLED],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Version' => 'Versión',
            'IdCatalog' => 'Catálogo',
            'IdState' => 'Estado',
            'CurrentVersion' => 'Versión Actual',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogdetails()
    {
        return $this->hasMany(Catalogdetail::class, ['IdCatalogVersion' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalog()
    {
        return $this->hasOne(Catalog::class, ['Id' => 'IdCatalog']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }

    public function getStates(){
        return ArrayHelper::map(State::find()->where(['KeyWord' => StringHelper::basename(self::class)])->asArray()->all(), 'Id', 'Name');
    }
}
