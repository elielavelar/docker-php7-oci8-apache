<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use common\models\Resource;
use Exception;

/**
 * This is the model class for table "incidentresource".
 *
 * @property int $Id
 * @property int $IdIncident
 * @property int $IdResource
 * @property string|null $Description
 *
 * @property Incident $incident
 * @property Resource $resource
 */
class Incidentresource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incidentresource';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdIncident', 'IdResource'], 'required'],
            [['IdIncident', 'IdResource'], 'integer'],
            [['Description'], 'string'],
            [['IdIncident'], 'exist', 'skipOnError' => true, 'targetClass' => Incident::class, 'targetAttribute' => ['IdIncident' => 'Id']],
            [['IdResource'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::class, 'targetAttribute' => ['IdResource' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('system', 'ID'),
            'IdIncident' => Yii::t('system', 'Id Incident'),
            'IdResource' => Yii::t('system', 'Id Resource'),
            'Description' => Yii::t('system', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncident()
    {
        return $this->hasOne(Incident::class, ['Id' => 'IdIncident']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResource()
    {
        return $this->hasOne(Resource::class, ['Id' => 'IdResource']);
    }
}
