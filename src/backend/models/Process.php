<?php

namespace backend\models;

use Yii;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\CustomActiveRecord;

/**
 * This is the model class for table "process".
 *
 * @property int $Id
 * @property string $Name
 * @property string $Code
 * @property int $IdState
 * @property string $Description
 *
 * @property Policy[] $policies
 * @property State $state
 * @property Processdetail[] $processdetails
 */
class Process extends CustomActiveRecord
{
    const STATE_ACTIVE = 'ACT';
    const STATE_INACTIVE = 'INA';
    
    public $processitems = [];
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'process';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Code', 'IdState'], 'required'],
            [['IdState'], 'integer'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 200],
            [['Code'], 'string', 'max' => 10],
            [['Code'], 'unique'],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Name' => 'Nombre',
            'Code' => 'Código',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
            'processitems' => 'Departamentos',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPolicies()
    {
        return $this->hasMany(Policy::className(), ['IdProcess' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $droptions = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcessdetails()
    {
        return $this->hasMany(Processdetail::className(), ['IdProcess' => 'Id']);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            $this->_saveDetails();
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    private function _saveDetails(){
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $itemsDelete = [];
            $itemsInsert = [];
            $currentItems = [];
            foreach ($this->processdetails as $det){
                array_push($currentItems, $det->IdServiceCentre);
                $itemsDelete = !in_array($det->IdServiceCentre, $this->processitems) ? array_merge($itemsDelete, [$det->IdServiceCentre]) : $itemsDelete;
            }
            if(!empty($this->processitems)){
                $details = [];
                foreach ($this->processitems as $key){
                    if(!in_array($key, $currentItems)){
                        $details = [
                            'IdProcess'=> $this->Id,
                            'IdServiceCentre' => $key,
                            'Enabled' => Processdetail::STATUS_ENABLED,
                        ];
                        $itemsInsert[] = $details;
                    }
                }
                foreach ($itemsInsert as $detail){
                    $det = new Processdetail();
                    $det->attributes = $detail;
                    if(!$det->save()){
                        throw new Exception(\Yii::$app->customFunctions->getErrors($det->errors),92000);
                    } 
                }
            } else {
                Processdetail::deleteAll(['IdProcess' => $this->Id]);
            }
            $transaction->commit();
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        
    }
}
