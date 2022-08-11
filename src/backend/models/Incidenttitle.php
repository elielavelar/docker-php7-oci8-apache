<?php

namespace backend\models;

use backend\models\traits\Incidenttitletrait;
use backend\models\traits\Incidenttrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "incidenttitle".
 *
 * @property int $Id
 * @property string $Title
 * @property int|null $IdCategoryType
 * @property string|null $Description
 * @property int $Enabled
 *
 * @property Incidentcategory $idCategoryType
 */
class Incidenttitle extends \yii\db\ActiveRecord
{
    use Incidenttitletrait;
    public $uploadFile = null;
    const ENABLED = 1;
    const DISABLED = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'incidenttitle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Title'], 'required'],
            [['IdCategoryType'], 'integer'],
            [['Description'], 'string'],
            [['Title'], 'string', 'max' => 255],
            [['IdCategoryType'], 'exist', 'skipOnError' => true, 'targetClass' => Incidentcategory::className(), 'targetAttribute' => ['IdCategoryType' => 'Id']],
            ['Enabled', 'default', 'value' => self::ENABLED ],
            ['Enabled', 'in', 'range' => [self::DISABLED, self::ENABLED] ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('system', 'Id'),
            'Title' => Yii::t('system', 'Title'),
            'IdCategoryType' => Yii::t('system', 'Type'),
            'Description' => Yii::t('system', 'Description'),
            'Enabled' => Yii::t('system', 'Enabled'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdCategoryType()
    {
        return $this->hasOne(Incidentcategory::className(), ['Id' => 'IdCategoryType']);
    }

    /**
     * @throws Exception
     */
    public static function getList($q = null, $idparent = null){
        $query = self::find()
            ->select(['Id as id',"Title as text"]);
        if( !empty($q) ){
            $query->where(['like',"Title", $q]);
        }
        if( $idparent ){
            $query->andWhere(['IdCategoryType' => $idparent]);
        }
        return $query
            ->orderBy(['Title' => SORT_ASC])
            ->asArray()
            ->all();
    }
}
