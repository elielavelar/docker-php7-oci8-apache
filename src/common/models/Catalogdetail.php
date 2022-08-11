<?php

namespace common\models;

use Yii;
use common\models\State;
use common\models\Type;
use common\models\CustomActiveRecord;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\Field;

/**
 * This is the model class for table "catalogdetails".
 *
 * @property int $Id
 * @property string $Name
 * @property int $IdCatalogVersion
 * @property string $KeyWord
 * @property string $Code
 * @property int $IdState
 * @property int $IdType
 * @property string $Description
 *
 * @property Catalogversion $catalogVersion
 * @property State $state
 * @property Type $type
 * @property Catalogdetailvalue[] $catalogdetailvalues
 * @property Type[] $dataTypes
 */
class Catalogdetail extends CustomActiveRecord
{
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'catalogdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'IdCatalogVersion', 'KeyWord','Code', 'IdState', 'IdType'], 'required'],
            [['IdCatalogVersion', 'IdState', 'IdType'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['KeyWord'], 'string', 'max' => 100],
            [['Code'], 'string', 'max' => 10],
            [['Code'], 'unique', 'targetAttribute' => ['IdCatalogVersion','KeyWord','Code'], 'message' => 'Ya existe el código {value} para la llave ingresada.'],
            [['IdCatalogVersion'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogversion::class, 'targetAttribute' => ['IdCatalogVersion' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdType' => 'Id']],
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
            'IdCatalogVersion' => 'Versión',
            'KeyWord' => 'Llave',
            'Code' => 'Código',
            'IdState' => 'Estado',
            'IdType' => 'Tipo',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogVersion()
    {
        return $this->hasOne(Catalogversion::class, ['Id' => 'IdCatalogVersion']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }

    public function getStates(){
        $droptions = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }

    public function getTypes(){
        $droptions = Type::find()
            ->joinWith('state b')
            ->where([
                'b.Code' => Type::STATUS_ACTIVE
                , 'b.KeyWord' => StringHelper::basename(Type::class)
                , Type::tableName().'.KeyWord' => StringHelper::basename(Field::class)
            ])
            ->orderBy([Type::tableName().'.Sort' => SORT_ASC])
            ->all();
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogdetailvalues()
    {
        return $this->hasMany(Catalogdetailvalue::class, ['IdCatalogDetail' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDataTypes()
    {
        return $this->hasMany(Type::class, ['Id' => 'IdDataType'])->viaTable(Catalogdetailvalue::tableName(), ['IdCatalogDetail' => 'Id']);
    }
}
