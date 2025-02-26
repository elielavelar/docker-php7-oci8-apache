<?php
namespace client\models;

use yii\base\Model;
use yii\base\InvalidParamException;
use frontend\models\Citizen;

/**
 * Password reset form
 */
class ResetPasswordForm extends Model
{
    public $password;

    /**
     * @var Citizen
     */
    private $_user;


    /**
     * Creates a form model given a token.
     *
     * @param string $token
     * @param array $config name-value pairs that will be used to initialize the object properties
     * @throws \yii\base\InvalidParamException if token is empty or not valid
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) {
            throw new InvalidParamException('Token para Restablecer contraseña no puede quedar vacío.');
        }
        $this->_user = Citizen::findByPasswordResetToken($token);
        if (!$this->_user) {
            throw new InvalidParamException('Token para Restablecer Contraseña Erróneo.');
        }
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['password', 'required','message'=>'Campo {attribute} no puede quedar vacío'],
            ['password', 'string', 'min' => 6],
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
        $user->setPassword($this->password);
        $user->removePasswordResetToken();

        return $user->save(false);
    }
}
