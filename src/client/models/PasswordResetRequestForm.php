<?php
namespace client\models;

use Yii;
use yii\base\Model;
use frontend\models\Citizen;
use common\models\State;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required','message'=>'{attribute} no puede quedar vacío'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\frontend\models\Citizen',
                #'filter' => ['IdState' => State::findOne(['KeyWord'=>'Citizen','Code'=>  Citizen::STATUS_ACTIVE])->Id],
                'message' => 'No existe un usuario con este correo electrónico.'
            ],
        ];
    }
    
    public function attributeLabels()
    {
        return [
            'email' => 'Correo Electrónico',
        ];

    }
    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = Citizen::findOne([
            #'IdState' => State::findOne(['KeyWord'=>'Citizen','Code'=>  Citizen::STATUS_ACTIVE])->Id,
            'Email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!Citizen::isPasswordResetTokenValid($user->PasswordResetToken)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        return Yii::$app
            ->mailer
            ->compose(
                ['html' => '@frontend/mail/passwordResetToken-html', 'text' => '@frontend/mail/passwordResetToken-text'],
                ['user' => $user]
            )
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
            ->setTo($this->email)
            ->setSubject('Restablecer contraseña para ' . Yii::$app->name)
            ->send();
    }
}
