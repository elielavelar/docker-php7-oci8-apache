<?php

namespace backend\models;

use common\models\State;
use yii\helpers\StringHelper;
use Exception;
use Yii;

/**
 * This is the model class for table "transactionmodel".
 *
 * @property int $Id
 * @property string $ModelName
 * @property string $NameSpace
 * @property string $AttributeKey
 * @property int $Enabled
 *
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
            [['ModelName', 'NameSpace', 'AttributeKey'], 'required'],
            [['Enabled'], 'integer'],
            [['Enabled'], 'default','value'=> self::STATUS_ACTIVE],
            [['ModelName'], 'string', 'max' => 50],
            [['NameSpace'], 'string', 'max' => 200],
            [['AttributeKey'], 'string', 'max' => 100],
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
            'ModelName' => 'Model Name',
            'NameSpace' => 'Name Space',
            'AttributeKey' => 'Attribute Key',
            'Enabled' => 'Habilitado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransactions()
    {
        return $this->hasMany(Transaction::className(), ['IdTransactionModel' => 'Id']);
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
                $model = self::findOne(['ModelName'=> $tmodel->ModelName,'NameSpace'=> $tmodel->NameSpace]);
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
                $model = self::findOne(['ModelName'=> $tmodel->ModelName,'NameSpace'=> $tmodel->NameSpace]);
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
