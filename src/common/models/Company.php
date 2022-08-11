<?php

namespace common\models;

use Yii;
use common\models\CustomActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "company".
 *
 * @property int $Id
 * @property string $Name
 * @property string|null $TaxRegistrationNumber Número de Registro Fiscal
 * @property int $IdSizeType
 * @property string|null $TaxIdentificationNumber Número de Identificación Tributaria (NIT)
 * @property string|null $TradeName
 * @property string|null $BusinessSector Giro
 * @property int $Enabled
 * @property string|null $Description
 *
 * @property Type $sizeType
 * @property-read mixed $sizeTypes
 * @property-read \yii\db\ActiveQuery $companyoptions
 * @property Companyuser[] $companyusers
 */
class Company extends CustomActiveRecord
{
    const ENABLED = 1;
    const DISABLED = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['Id' => $id, 'Enabled' => self::ENABLED ]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'IdSizeType'], 'required'],
            [['IdSizeType', 'Enabled'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['TaxRegistrationNumber', 'TaxIdentificationNumber'], 'string', 'max' => 20],
            [['TradeName', 'BusinessSector'], 'string', 'max' => 200],
            [['IdSizeType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdSizeType' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'TaxRegistrationNumber' => 'Registro Fiscal',
            'IdSizeType' => 'Tamaño',
            'TaxIdentificationNumber' => 'Tax Identification Number',
            'TradeName' => 'Trade Name',
            'BusinessSector' => 'Business Sector',
            'Enabled' => 'Enabled',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSizeType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdSizeType']);
    }
    
    public function getSizeTypes(){
        $types = Type::find()
                ->joinWith('state b')
                ->where([
                    Type::tableName().'.KeyWord' => StringHelper::basename(self::class).'Size',
                    'b.Code' => Type::STATUS_ACTIVE,
                ])
                ->asArray()
                ->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyoptions()
    {
        return $this->hasMany(Companyoption::class, ['IdCompany' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompanyusers()
    {
        return $this->hasMany(Companyuser::class, ['IdCompany' => 'Id']);
    }
}
