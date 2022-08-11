<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "companyuser".
 *
 * @property int $Id
 * @property int $IdUser
 * @property int $IdCompany
 *
 * @property Company $company
 * @property-read mixed $companies
 * @property User $user
 */
class Companyuser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'companyuser';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdUser', 'IdCompany'], 'required'],
            [['IdUser', 'IdCompany'], 'integer'],
            [['IdCompany'], 'exist', 'skipOnError' => true, 'targetClass' => Company::class, 'targetAttribute' => ['IdCompany' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['IdUser' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdUser' => 'Id User',
            'IdCompany' => 'Id Company',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::class, ['Id' => 'IdCompany']);
    }

    public function getCompanies(){
        $companies = Company::find()
            ->where([
                'Enabled' => Company::ENABLED,
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($companies, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUser']);
    }
}
