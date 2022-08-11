<?php
namespace common\models;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

/**
 * Class CompanyForm
 * @package common\models
 *
 * @property User $_user
 */

class CompanyForm extends Model
{
    public $idcompany;
    private $_user;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idcompany'], 'required'],
            [['idcompany'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'idcompany'=>'Organización',
        ];
    }


    private function _setUser(){
        $this->_user = Yii::$app->getUser()->getIdentity();
    }

    public function getCompanies(){
        $this->_setUser();
        $companies = Companyuser::find()
                        ->select(['b.Id', 'b.Name'])
                        ->joinWith('company b', false)
                        ->where([
                            'b.Enabled' => Company::ENABLED,
                            Companyuser::tableName().'.IdUser' => $this->_user->Id,
                        ])->asArray()->all();
        return ArrayHelper::map($companies, 'Id','Name');
    }

    public function setCompany(){
        $this->_setUser();
        $company = Company::findOne(['Id' => $this->idcompany]);
        Yii::$app->getUser()->getIdentity()->setCompany($company);
        //$this->_user->setCompany($company);
        return !empty($company);
    }
}