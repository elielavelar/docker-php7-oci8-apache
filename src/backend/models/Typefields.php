<?php

namespace backend\models;

use Yii;
use common\models\State;
use common\models\Type;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\Fields;

/**
 * This is the model class for table "typefields".
 *
 * @property int $Id
 * @property int $IdType
 * @property int $IdField
 * @property int $IdState
 * @property string $CustomLabel
 * @property int $Required
 * @property int $Sort
 * @property int $ColSpan
 * @property int $RowSpan
 * @property string $CssClass
 * @property string $Description
 *
 * @property State $state
 * @property Fields $field
 * @property Type $type
 */
class Typefields extends \yii\db\ActiveRecord
{
    
    const REQUIRE_VALUE_FALSE = 0;
    const REQUIRE_VALUE_TRUE = 1;
    
    const DEFAULT_SORT_VALUE = 1;
    
    const DEFAULT_COLSPAN = 6;
    const DEFAULT_ROWSPAN = 1;
    
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'typefields';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdType', 'IdField', 'IdState'], 'required'],
            [['IdType', 'IdField', 'IdState', 'Required', 'Sort', 'ColSpan', 'RowSpan'], 'integer'],
            [['Description'], 'string'],
            [['CustomLabel'], 'string', 'max' => 100],
            [['CssClass'], 'string', 'max' => 50],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdField'], 'exist', 'skipOnError' => true, 'targetClass' => Fields::className(), 'targetAttribute' => ['IdField' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdType' => 'Id']],
            ['Sort','default', 'value' => self::DEFAULT_SORT_VALUE],
            ['RowSpan', 'default', 'value' => self::DEFAULT_ROWSPAN],
            ['ColSpan', 'default', 'value' => self::DEFAULT_COLSPAN],
            ['Required', 'default', 'value' => self::REQUIRE_VALUE_TRUE],
            [['Required'],'in','range'=>[self::REQUIRE_VALUE_FALSE, self::REQUIRE_VALUE_TRUE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdType' => 'Tipo',
            'IdField' => 'Campo',
            'IdState' => 'Estado',
            'CustomLabel' => 'Etiqueta',
            'Required' => 'Requerido',
            'Sort' => 'Orden',
            'ColSpan' => 'Col Span',
            'RowSpan' => 'Row Span',
            'CssClass' => 'Css Class',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Fields::className(), ['Id' => 'IdField']);
    }
    
    public function getFields($keyword = null){
        $fields = Fields::find()
                ->joinWith('state b', false)
                ->where(['b.Code' => Fields::STATUS_ACTIVE]);
        if($keyword){
            $fields->andWhere(['fields.KeyWord' => $keyword]);
        }
        $options = $fields->all();
        return ArrayHelper::map($options, 'Id', 'Name');
        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdType']);
    }
}
