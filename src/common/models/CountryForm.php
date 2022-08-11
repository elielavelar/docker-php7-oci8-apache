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
 * @property Country $_country
 */

class CountryForm extends Model
{
    public $idcountry;
    private $_user;
    private $_country;
    private $_countries = [];
    private $_countriesCount = [];
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idcountry'], 'required'],
            [['idcountry'], 'integer'],
        ];
    }

    public function attributeLabels() {
        return [
            'idcountry'=>'País',
        ];
    }


    private function _setUser(){
        $this->_user = Yii::$app->getUser()->getIdentity();
    }

    private function _setCountry(){
        $this->_country = Yii::$app->getCountry()->getIdentity();
    }

    public function getCountries(){
        $this->_setUser();
        $this->_setCountriesList();
        return ArrayHelper::map($this->_countries, 'Id','Name');
    }

    public function setCountry(){
        $this->_setUser();
        $country = Country::findOne(['Id' => $this->idcountry]);
        Yii::$app->getCountry()->setCountry($country);
        return !empty($country);
    }
    
    private function _setCountriesList(){
        try {
            $this->_countries = Countryuser::find()
                        ->select(['b.Id', 'b.Name'])
                        ->joinWith('country b', false)
                        ->innerJoin(State::tableName().' c','b.IdState = c.Id')
                        ->where([
                            'c.Code' => Country::STATE_ACTIVE,
                            Countryuser::tableName().'.IdUser' => $this->_user->Id,
                        ])->asArray()->all();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function validatteEnabledCountry(){
        try {
            $this->_setUser();
            $this->_setCountriesList();
            $this->_countriesCount = (count($this->_countries));
            return ($this->_countriesCount > 0);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function setDefaultCountry(){
        $countryuser = reset($this->_countries);
        $country = Country::findOne(['Id' => $countryuser['Id']]);
        Yii::$app->getCountry()->setCountry($country);
    }
}