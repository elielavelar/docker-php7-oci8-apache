<?php

namespace common\models;

use Yii;
use common\models\Registredmodel;
use Exception;

/**
 * This is the model class for table "transactionmodel".
 *
 * @property int $Id
 * @property int $IdRegistredModel
 * @property int $Enabled
 *
 * @property Registredmodel $registredModel
 * @property Transaction[] $transactions
 */
class Transactionmodel extends \yii\db\ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactionmodel';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdRegistredModel'], 'required'],
            [['IdRegistredModel','Enabled'], 'integer'],
            [['IdRegistredModel'], 'exist', 'skipOnError' => true, 'targetClass' => Registredmodel::class, 'targetAttribute' => ['IdRegistredModel' => 'Id']],
            [['Enabled'], 'default','value'=> self::STATUS_ACTIVE],
            [['Enabled'],'in','range'=>[self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdRegistredModel' => 'Modelo',
            'Enabled' => 'Habilitado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistredModel()
    {
        return $this->hasOne(Registredmodel::class, ['Id' => 'IdRegistredModel']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::class, ['IdTransactionModel' => 'Id']);
    }
    
    public function beforeSave($insert) {
        try {
            
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function validateTransactionModel($tmodel = NULL){
        try {
            if($tmodel){
                $model = self::findOne(['IdRegistredModel'=> $tmodel->IdRegistredModel]);
                return $model ? ($model->Enabled == self::STATUS_ACTIVE):TRUE;
            } else {
                return TRUE;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function getTransactionModel($tmodel = NULL){
        try {
            if($tmodel){
                $model = self::findOne(['IdRegistredModel'=> $tmodel->IdRegistredModel]);
                if(!$model){
                    if(!$tmodel->save()){
                        $message = Yii::$app->customFunctions->getErrors($tmodel->errors);
                        throw new Exception('ERROR: '.$message, 92002);
                    }
                    $tmodel->refresh();
                    $model = $tmodel;
                } 
                return $model;
            } else {
                throw new Exception("No se definió model de transacción",92001);
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
