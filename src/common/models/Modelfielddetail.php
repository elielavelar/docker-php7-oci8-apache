<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "modelfielddetails".
 *
 * @property int $Id
 * @property int $IdModelField
 * @property int $IdField
 * @property int $IdState
 * @property int $Required
 * @property string $CustomLabel
 * @property int $Sort
 * @property int $Colspan
 * @property int $Rowspan
 * @property string $CssClass
 * @property string $Description
 *
 * @property Field $field
 * @property Modelfield $modelField
 * @property State $state
 */
class Modelfielddetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'modelfielddetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdModelField', 'IdField', 'IdState'], 'required'],
            [['IdModelField', 'IdField', 'IdState', 'Required', 'Sort', 'Colspan', 'Rowspan'], 'integer'],
            [['Description'], 'string'],
            [['CustomLabel'], 'string', 'max' => 100],
            [['CssClass'], 'string', 'max' => 50],
            [['IdField'], 'exist', 'skipOnError' => true, 'targetClass' => Field::class, 'targetAttribute' => ['IdField' => 'Id']],
            [['IdModelField'], 'exist', 'skipOnError' => true, 'targetClass' => Modelfield::class, 'targetAttribute' => ['IdModelField' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class, 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdModelField' => 'Id Model Field',
            'IdField' => 'Id Field',
            'IdState' => 'Id State',
            'Required' => 'Required',
            'CustomLabel' => 'Custom Label',
            'Sort' => 'Sort',
            'Colspan' => 'Colspan',
            'Rowspan' => 'Rowspan',
            'CssClass' => 'Css Class',
            'Description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getField()
    {
        return $this->hasOne(Field::class, ['Id' => 'IdField']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModelField()
    {
        return $this->hasOne(Modelfield::class, ['Id' => 'IdModelField']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }
}
