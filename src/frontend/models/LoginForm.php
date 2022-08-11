<?php
namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $authkey;
    public $rememberMe = true;

    private $_user;

    const SCENARIO_WEBSERVICE = 'webservice';
    
    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_WEBSERVICE] = ['authkey'];
        
        return $scenarios;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required','message'=>'{attribute} no puede quedar vacío'],
            // rememberMe must be a boolean value
            [['authkey'], 'required','on'=>'webservice'],
            ['authkey', 'string'],
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Usuario',
            'password' => 'Contraseña',
            'rememberMe' => 'Recordarme',
        ];
    }
    
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Usuario o Contraseña Incorrectos');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }
    
    /**
     * Logs in a user using the provided authentication key
     *
     * @return bool whether the user is logged in successfully
     */
    public function loginByKey(){
        if($this->validate()){
            return Yii::$app->user->login($this->getUserByAuthKey(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return FALSE;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
    
    /**
     * Finds user by [[authkey]]
     *
     * @return User|null
     */
    protected function getUserByAuthKey(){
        if($this->_user === NULL){
            $this->_user = User::findIdentityByAuthKey($this->authkey);
        }
        return $this->_user;
    }

}
