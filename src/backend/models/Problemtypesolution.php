<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "problemtypesolution".
 *
 * @property int $Id
 * @property int $IdProblemType
 * @property string $Name
 * @property string $Code
 * @property int $Enabled
 * @property string|null $Description
 *
 * @property Problemtype $problemtype
 */
class Problemtypesolution extends \yii\db\ActiveRecord
{
    const ENABLED = 1;
    const DISABLED = 0;

    public $uploadFile = null;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'problemtypesolution';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdProblemType', 'Name', 'Code'], 'required'],
            [['IdProblemType', 'Enabled'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 500],
            [['Code'], 'string', 'max' => 20],
            [['Code'], 'unique'],
            [['IdProblemType'], 'exist', 'skipOnError' => true, 'targetClass' => Problemtype::class, 'targetAttribute' => ['IdProblemType' => 'Id']],
            ['Enabled', 'default', 'value' => self::ENABLED ],
            ['Enabled', 'range', 'in' => [ self::DISABLED, self::ENABLED] ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('system', 'ID'),
            'IdProblemType' => Yii::t('system', 'Id Problem'),
            'Name' => Yii::t('system', 'Name'),
            'Code' => Yii::t('system', 'Code'),
            'Enabled' => Yii::t('system', 'Enabled'),
            'Description' => Yii::t('system', 'Description'),
            'uploadFile' => 'Cargar desde Archivo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdProblemType()
    {
        return $this->hasOne(Problemtype::class, ['Id' => 'IdProblemType']);
    }

    public function getStates(){
        return [ self::DISABLED => 'No', self::ENABLED => 'Sí'];
    }
}
