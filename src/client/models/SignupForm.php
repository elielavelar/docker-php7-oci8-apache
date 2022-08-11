<?php
namespace client\models;

use yii\base\Model;
use frontend\models\Citizen;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $name;
    public $lastname;
    public $email;
    public $password;
    public $passwordconfirm;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'trim'],
            [['name','lastname','email','password','passwordconfirm'], 'required','message'=>'{attribute} no puede quedar vacío'],
            ['name', 'string', 'min' => 2, 'max' => 255],
            
            ['lastname', 'trim'],
            ['lastname', 'string', 'min' => 2, 'max' => 255],
            
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\frontend\models\Citizen','targetAttribute' => 'Email', 'message' => 'Email ya  existe.'],

            ['password', 'string', 'min' => 6],
            
            ['passwordconfirm', 'string', 'min' => 6],
            ['passwordconfirm', 'compare', 'compareAttribute'=>'password', 'message'=>"Contraseñas no coinciden" ],
        ];
    }
    
     public function attributeLabels()
    {
        return [
            'name' => 'Nombres',
            'lastname' => 'Apellidos',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'passwordconfirm' => 'Confirmar Contraseña',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }
        
        $user = new Citizen();
        $user->scenario = Citizen::SCENARIO_SIGNUP;
        $user->Name = $this->name;
        $user->LastName = $this->lastname;
        $user->Email = $this->email;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        
        if($user->save()){
            return $user;
        } else {
            print_r($user->attributes);
            echo "<br>";
            print_r($user->errors);
            return NULL;
        }
    }
}
