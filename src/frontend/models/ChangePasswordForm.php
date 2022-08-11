<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace frontend\models;
use common\models\User;
use kartik\password\StrengthValidator;

/**
 * Description of ChangePasswordForm
 *
 * @author avelare
 */
use yii\base\Model;

class ChangePasswordForm extends Model {
    //put your code here
    private $_user;
    public $username;
    public $oldPassword;
    public $newPassword;
    public $confirmNewPassword;
    
    function __construct($config = array()) {
        $this->_user = \Yii::$app->getUser()->getIdentity();
        //$this->_user = User::findByUsername($username);
        $this->username = $this->_user->Username;
        if (!$this->_user) {
            throw new InvalidParamException('Usuario no Encontrado');
        }
        parent::__construct($config);
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            [['username','oldPassword','newPassword','confirmNewPassword'], 'required','message'=>'{attribute} no puede quedar vacío'],
            
            [['newPassword','confirmNewPassword'], 'trim'],
            [['newPassword','confirmNewPassword'], 'string', 'min' => 8, 'max' => 255],
            ['newPassword', StrengthValidator::className(),'preset'=>'normal','userAttribute'=>'username'],
            
            ['confirmNewPassword', 'compare', 'compareAttribute'=>'newPassword', 'message'=>"Contraseñas no coinciden" ],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'username' => 'Nombre de Usuario',
            'oldPassword' => 'Contraseña Anterior',
            'newPassword' => 'Nueva Contraseña',
            'confirmNewPassword' => 'Confirmar Nueva Contraseña',
        ];
    }
    
    /**
     * Resets password.
     *
     * @return bool if password was reset.
     */
    public function resetPassword()
    {
        $user = $this->_user;
        $user->setPassword($this->newPassword);
        return $user->save(false);
    }
    
    public function setPassword(){
        try {
            $user = $this->_user;
            if(!$user->comparePasswords($this->newPassword)){
                $user->_password = $this->newPassword;
                return $user->save();
            } else {
                $this->addError('newPassword', 'Contraseña Nueva es igual que la Anterior');
                return FALSE;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        
        
    }
    
}
