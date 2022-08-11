<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "resourcetype".
 *
 * @property int $Id
 * @property int $IdType
 * @property string $Name
 * @property string $KeyWord
 * @property string $Code
 * @property int $IdState
 * @property int $AgroupationType
 * @property int|null $IdParent
 * @property int $EnableMonitoring
 * @property string|null $Description
 *
 * @property Resourcetype $parent
 * @property Resourcetype[] $resourcetypes
 * @property State $state
 * @property Type $type
 */
class Resourcetype extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resourcetype';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdType', 'Name', 'KeyWord', 'Code', 'IdState'], 'required'],
            [['IdType', 'IdState', 'AgroupationType', 'IdParent'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['Code'], 'string', 'max' => 20],
            [['KeyWord'], 'string', 'max' => 50],
            [['KeyWord'], 'unique'],
            [['IdType', 'Code'], 'unique', 'targetAttribute' => ['IdType', 'Code']],
            [['IdParent'], 'exist', 'skipOnError' => true, 'targetClass' => Resourcetype::class, 'targetAttribute' => ['IdParent' => 'Id']],
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
            'Id' => Yii::t('system', 'Id'),
            'IdType' => Yii::t('system', 'Type'),
            'Name' => Yii::t('app', 'Name'),
            'KeyWord' => Yii::t('app', 'KeyWord'),
            'Code' => Yii::t('app', 'Code'),
            'IdState' => Yii::t('system', 'State'),
            'AgroupationType' => Yii::t('system', 'Agroupation Type'),
            'IdParent' => Yii::t('app', 'Parent'),
            'Description' => Yii::t('system', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdParent()
    {
        return $this->hasOne(Resourcetype::class, ['Id' => 'IdParent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResourcetypes()
    {
        return $this->hasMany(Resourcetype::class, ['IdParent' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }


    /**
     * @return array
     */
    public function getStates()
    {
        $states = State::find()
            ->select(['Id', 'Name'])
            ->where([
                'KeyWord' => StringHelper::basename(Resource::class),
            ])->asArray()->all();
        return ArrayHelper::map($states, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }

    /**
     * @return array
     */
    public function getTypes(): array
    {
        $types = Type::find()
            ->select([ Type::tableName().'.Id', Type::tableName().'.Name'])
            ->innerJoin(State::tableName().' a', Type::tableName().'.IdState = a.Id ')
            ->where([
                Type::tableName().'.KeyWord' => StringHelper::basename(Resource::class),
                'a.Code' => Type::STATUS_ACTIVE,
        ])->asArray()->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }
}
