<?php

namespace common\models;

use common\models\CustomActiveRecord;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\User;
use Exception;


/**
 * This is the model class for table "TBLOPERATORS".
 *
 * @property string $CODOPER
 * @property string $PASSWD
 * @property int $USERID
 * @property int $IDPROF
 * @property int $STATUS
 * @property string $CREATIONDATE
 * @property string $EXPIREDATEPASS
 * @property string $EXPIREDATEUSR
 * @property string $CODEMPLEADO
 */

class Tbloperator extends ActiveRecord {
    //put your code here
    public $password = NULL;
    public $PasswordHash = NULL;
    public $_expirationDate = NULL;
    
    public static function getDb() {
        return Yii::$app->muhlbauer;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'TBLOPERATORS';
    }
    
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'EXPIREDATEPASS',
                ],
                'value' => function ($event) {
                    return new Expression("TO_DATE('" . $this->EXPIREDATEPASS . "','YYYY-MM-DD')");
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['USERID', 'CODOPER'], 'required'],
            [['USERID','STATUS','IDPROF'], 'integer'],
            [['CREATIONDATE', 'EXPIREDATEPASS'], 'string'],
            [['CODOPER'], 'string', 'max' => 50],
            [['CODOPER'], 'unique'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'USERID' => 'Código de Usuario',
            'CODOPER' => 'Código de Operador',
            'CREATIONDATE' => 'Fecha de Creación',
            'EXPIREDATEPASS' => 'Fecha de Expiración de Contraseña',
            'STATUS' => 'Estado',
            'IDPROF' => 'Id Perfil',
        ];
    }
    
    public function comparePass(){
        try {
            $hash = $this->getPasswordHash();
            return ($this->PASSWD == $hash);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getPasswordHash(){
        try {
            $pass = $this->USERID. $this->password;
            $query = "SELECT md5hash('$pass') as HASH FROM DUAL";
            $result = $this->getDb()->createCommand($query)->queryOne();
            return $result["HASH"];
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function afterFind() {
        if(!empty($this->EXPIREDATEPASS)){
            $date = \DateTime::createFromFormat('d/m/Y', $this->EXPIREDATEPASS);
            $this->EXPIREDATEPASS = $date->format('Y-m-d H:i:s');
        }
        return parent::afterFind();
    }


    public function updatePassword(){
        try {
            $this->PASSWD = $this->getPasswordHash();
            if(!$this->save()){
                $message = Yii::$app->customFunctions->getErrors($this->errors);
                throw new Exception($message, 99001);
            } 
            $this->refresh();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _updateExpirationDate(){
        try {
            $codoper = $this->CODOPER;
            $sql = "UPDATE ".$this->tableName()." "
                    . " SET EXPIREDATEPASS = TO_DATE(:dateexp, 'YYYY-MM-DD')"
                    . " WHERE CODOPER = :codoper ";
            
            $query = parent::getDb()->createCommand($sql);
            $query->bindParam(':dateexp', $this->_expirationDate);
            $query->bindParam(':codoper', $codoper);
            $query->execute();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
   
}
