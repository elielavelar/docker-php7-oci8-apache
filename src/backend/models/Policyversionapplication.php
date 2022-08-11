<?php

namespace backend\models;

use Yii;
use common\models\Catalogdetails;

/**
 * This is the model class for table "policyversionapplication".
 *
 * @property int $Id
 * @property int $IdPolicyVersion
 * @property int $IdCatalogDetail
 * @property int $IdRecord
 *
 * @property Catalogdetails $catalogDetail
 * @property Policyversions $policyVersion
 */
class Policyversionapplication extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'policyversionapplication';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdPolicyVersion', 'IdCatalogDetail'], 'required'],
            [['Id', 'IdPolicyVersion', 'IdCatalogDetail', 'IdRecord'], 'integer'],
            [['Id'], 'unique'],
            [['IdCatalogDetail'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetails::className(), 'targetAttribute' => ['IdCatalogDetail' => 'Id']],
            [['IdPolicyVersion'], 'exist', 'skipOnError' => true, 'targetClass' => Policyversions::className(), 'targetAttribute' => ['IdPolicyVersion' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdPolicyVersion' => 'Id Policy Version',
            'IdCatalogDetail' => 'Id Catalog Detail',
            'IdRecord' => 'Id Record',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogDetail()
    {
        return $this->hasOne(Catalogdetails::className(), ['Id' => 'IdCatalogDetail']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPolicyVersion()
    {
        return $this->hasOne(Policyversions::className(), ['Id' => 'IdPolicyVersion']);
    }
}
