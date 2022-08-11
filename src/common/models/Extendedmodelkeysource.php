<?php

namespace common\models;

use Yii;
use common\models\Type;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "extendedmodelkeysource".
 *
 * @property int $Id
 * @property int $IdExtendedModelKey
 * @property int $IdRegistredModel
 * @property string $TableAlias
 * @property string $RelationString Default value is the Table Key Attribute Name
 * @property int $CustomRelationString
 * @property string $ReturnAttributeKeyId
 * @property string $ReturnAttributeKeyText
 * @property int $Sort
 * @property int $IdJoinType
 * @property string $Description
 *
 * @property Extendedmodelkeycondition[] $extendedmodelkeyconditions
 * @property Extendedmodelkey $extendedModelKey
 * @property Type $joinType
 * @property Registredmodel $registredModel
 */
class Extendedmodelkeysource extends \yii\db\ActiveRecord
{
    const JOIN_TYPE_KEYWORD = 'SQLJoinType';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'extendedmodelkeysource';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdExtendedModelKey', 'IdRegistredModel'], 'required'],
            [['IdExtendedModelKey', 'IdRegistredModel', 'CustomRelationString', 'Sort', 'IdJoinType'], 'integer'],
            [['Description'], 'string'],
            [['TableAlias'], 'string', 'max' => 15],
            [['RelationString'], 'string', 'max' => 100],
            [['ReturnAttributeKeyId', 'ReturnAttributeKeyText'], 'string', 'max' => 50],
            [['IdExtendedModelKey'], 'exist', 'skipOnError' => true, 'targetClass' => Extendedmodelkey::class, 'targetAttribute' => ['IdExtendedModelKey' => 'Id']],
            [['IdJoinType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class, 'targetAttribute' => ['IdJoinType' => 'Id']],
            [['IdRegistredModel'], 'exist', 'skipOnError' => true, 'targetClass' => Registredmodel::class, 'targetAttribute' => ['IdRegistredModel' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdExtendedModelKey' => 'Llave',
            'IdRegistredModel' => 'Modelo',
            'TableAlias' => 'Alias de Tabla',
            'RelationString' => 'Cadena de Relación',
            'CustomRelationString' => 'Relación Personalizada',
            'ReturnAttributeKeyId' => 'Atributo Id Retornado',
            'ReturnAttributeKeyText' => 'Atributo Texto Retornado',
            'Sort' => 'Orden',
            'IdJoinType' => 'Tipo de Unión',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedmodelkeyconditions()
    {
        return $this->hasMany(Extendedmodelkeycondition::class, ['IdExtendedModelKeySource' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getExtendedModelKey()
    {
        return $this->hasOne(Extendedmodelkey::class, ['Id' => 'IdExtendedModelKey']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getJoinType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdJoinType']);
    }
    
    public function getJoinTypeList(){
        $type = Type::find()
                ->joinWith('state b')
                ->where([
                    'b.Code' => Type::STATUS_ACTIVE,
                    Type::tableName().'.KeyWord' => self::JOIN_TYPE_KEYWORD,
                ])
                ->all();
        return ArrayHelper::map($type, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistredModel()
    {
        return $this->hasOne(Registredmodel::class, ['Id' => 'IdRegistredModel']);
    }
}
