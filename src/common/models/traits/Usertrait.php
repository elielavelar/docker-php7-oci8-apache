<?php
namespace common\models\traits;

use Yii;
use common\models\User;
use backend\models\Useroption;
use backend\models\Profileoption;
use backend\models\Setting;
use backend\models\Option;
use moonland\phpexcel\Excel;
use common\models\State;
use common\models\Profile;
use common\models\Servicecentre;

use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * @var $this User
 */
trait Usertrait {
    private $defaultValues = [];
    private $_session = null;
    private $useroptions = null;

    public function getUserMenu(){
        try {
            $this->_session = Yii::$app->session;
            $items = $this->_session->get('itemsMenu');
            $this->_setSubMenuSettings();
            if(empty($items)){
                $this->_getUserOptions();
            }
        } catch (\Throwable $th){
            throw $th;
        }
    }

    private function _getUserOptions(){
        try {
            $this->useroptions = new Useroption();
            $this->useroptions->IdUser = $this->Id;
            $this->useroptions->IdOption = null;
            $itemsMenu = $this->useroptions->loadMenu();
            $this->_session->set('itemsMenu', $itemsMenu);
        } catch (\Throwable $th){
            throw $th;
        }
    }

    private function _getProfileOptions(){
        try {
            $this->_session;
        } catch (\Throwable $th){
            throw $th;
        }
    }

    public function selectCompany(){
        try {
            if(count($this->companiesuser) > 1){
                return true;
            } else {
                $companies = $this->companiesuser;
                $c = reset($companies);
                $this->setCompany($c->company);
                return false;
            }
        } catch (\Throwable $th){
            throw $th;
        }
    }

    public function _setSubMenuSettings(){
        try {
            $session = \Yii::$app->session;
            $settings = Setting::find()
                    ->where(['KeyWord' => StringHelper::basename(Option::class), 'Code' => Option::SUBMENU_OPTION])->one();
            $submenu = [];
            if(!empty($settings)){
                foreach ($settings->settingdetails as $detail){
                    $submenu[$detail->Code] = $detail->Value;
                }
            }
            $session->set('subMenu', $submenu);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function upload(): bool
    {
        set_time_limit(600);
        $transaction = Yii::$app->getDb()->beginTransaction();
        try {
            $this->_getDefaultData();
            $data = Excel::import($this->uploadFile->tempName, ['setFirstRecordAsKeys'=>TRUE,'setIndexSheetByName'=>TRUE,]);
            foreach ($data as $sheet => $lines){
                $user = new User();
                $user->attributes = $lines;
                $this->_loadDefaultData($user);
                if(!$user->save()){
                    $message = Yii::$app->customFunctions->getErrors($user->errors);
                    $this->addError('uploadFile',$message);
                    throw new Exception($message, 95000);
                }
            }
            $transaction->commit();
            return true;
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    private function _getDefaultData(){
        try {
            $this->defaultValues['IdState'] = (int) State::find()->where(['KeyWord' => 'User', 'Code' => User::STATE_ACTIVE])->select('Id')->scalar();
            $this->defaultValues['IdProfile'] = (int) Profile::find()->where(['Code' => User::PROFILE_USER])->select('Id')->scalar();

        } catch (Exception $ex) {
            throw $ex;
        }
    }
    /**
     *
     * @param User $user
     * @throws Exception
     */
    private function _loadDefaultData(&$user){
        try {
            $user->generateAuthKey();
            $passwordHash = Yii::$app->security->generateRandomString(8);
            $user->setPassword($passwordHash);
            $user->IdState = $this->defaultValues['IdState'];
            $user->IdProfile = empty($user->IdProfile) ? $this->defaultValues['IdProfile'] : $user->IdProfile;
            if(empty($user->Username) && $user->IdServiceCentre){
                $user->Username = strtoupper($user->LastName.substr($user->FirstName, 0,1));
            }
            $user->Birthdate = \Yii::$app->getFormatter()->asDate(implode('-', explode('/', $user->Birthdate)), 'php:d-m-Y');
            $date = date_create(date('Y-m-d'));
            date_add($date, date_interval_create_from_date_string("1 year"));
            $user->PasswordExpirationDate = $date->format('d-m-Y');
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public static function getFilterUser($params = []){
        $idservicecentre = ArrayHelper::getValue($params, 'IdServiceCentre');
        $q = ArrayHelper::getValue($params, 'q', '');
        $tableName = self::getTableName();
        $query = User::find()
            ->innerJoin( State::tableName().' b', $tableName.'.IdState = b.Id')
            ->select([ $tableName.".Id as id", $tableName.".DisplayName as text"])
            ->where(['like',"CONCAT(FirstName,' ',LastName)", $q])
            ->andWhere([
                'b.Code' => self::STATE_ACTIVE
            ])
            ->orWhere(['like',"Username", $q])
            ->orWhere(['like',"Displayname", $q]);
        if( $idservicecentre ){
            $query->andWhere("(:service IS NULL OR :service = IdServiceCentre )", [':service'=> $idservicecentre]);
        }
        return $query->asArray()
            ->all();
    }
}