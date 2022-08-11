<?php

namespace backend\models;

use Yii;
use common\models\State;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use common\models\Attachment;
use Exception;

/**
 * This is the model class for table "policyversion".
 *
 * @property int $Id
 * @property string $Version
 * @property int $IdPolicy
 * @property int $IdState
 * @property string $Description
 * @property int $Approved
 * @property int $Sent
 * @property string $ApprovedDate
 * @property string $SentDate
 * @property int $ActualVersion
 * @property int $IdEarlierVersion
 *
 * @property State $state
 * @property Policy $policy
 * @property Policyversion $earlierversion
 * @property Policyversionapplication[] $policyversionapplications
 * @property-read mixed $states
 * @property Policyversion[] $policyversions
 */
class Policyversion extends \yii\db\ActiveRecord
{
    
    const ACTUAL_VERSION_ENABLED = 1;
    const ACTUAL_VERSION_DISABLED = 0;
    
    const STATE_INPROCESS = 'PROC';
    const STATE_ACTIVE = 'ACT';
    const STATE_OBSOLETE = 'OBS';
    
    const APPROVED_ENABLED = 1;
    const APPROVED_DISABLED = 0;
    
    const SENT_ENABLED = 1;
    const SENT_DISABLED = 0;
    
    public $attachment = NULL;
    public $path = NULL;
    
    public $EarlierVersionName = NULL;
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'policyversion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPolicy', 'IdState'], 'required'],
            [['IdPolicy', 'IdState', 'Approved', 'Sent', 'ActualVersion','IdEarlierVersion'], 'integer'],
            [['Description'], 'string'],
            [['ApprovedDate', 'SentDate'], 'safe'],
            [['Version'], 'string', 'max' => 10],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdPolicy'], 'exist', 'skipOnError' => true, 'targetClass' => Policy::className(), 'targetAttribute' => ['IdPolicy' => 'Id']],
            [['IdEarlierVersion'], 'exist', 'skipOnError' => true, 'targetClass' => self::class, 'targetAttribute' => ['IdEarlierVersion' => 'Id']],
            ['Approved', 'in', 'range' => [self::APPROVED_DISABLED, self::APPROVED_ENABLED]],
            [['Approved'], 'default','value'=> self::APPROVED_DISABLED],
            ['Sent', 'in', 'range' => [self::SENT_DISABLED, self::SENT_ENABLED]],
            [['Sent'], 'default','value'=> self::SENT_DISABLED],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'Version' => 'Versión',
            'IdPolicy' => 'Padre',
            'IdState' => 'Estado',
            'Description' => 'Descripción',
            'Approved' => 'Aprobado',
            'Sent' => 'Enviado',
            'ApprovedDate' => 'Fecha Aprobado',
            'SentDate' => 'Fecha Enviado',
            'ActualVersion' => 'Versión Actual',
            'IdEarlierVersion' => 'Version Anterior',
            'attachment' => 'Archivo Adjunto',
        ];
    }
    
     /**
     * @return \yii\db\ActiveQuery
     */
    public function getPolicyversionapplications()
    {
        return $this->hasMany(Policyversionapplication::className(), ['IdPolicyVersion' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $droptions = State::findAll([
            'KeyWord' => StringHelper::basename(self::class),
        ]);
        return ArrayHelper::map($droptions, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPolicy()
    {
        return $this->hasOne(Policy::className(), ['Id' => 'IdPolicy']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEarlierversion()
    {
        return $this->hasOne(self::class, ['Id' => 'IdEarlierVersion']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPolicyversions()
    {
        return $this->hasMany(Policyversion::className(), ['IdEarlierVersion' => 'Id']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachment()
    {
        $this->attachment = Attachment::find()
                ->where(['KeyWord' => StringHelper::basename(self::class), 'AttributeName' => 'Id', 'AttributeValue' => $this->Id])
                ->one();
        if($this->attachment){
            $this->path = $this->attachment->path;
        }
    }
    
    public function afterFind() {
        try {
            $this->_formatDate();
            $this->getAttachment();
            if($this->IdEarlierVersion){
                $this->EarlierVersionName = $this->IdEarlierVersion ? $this->earlierversion->Version:$this->IdEarlierVersion;
            }
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterFind();
    }
    
    public function beforeSave($insert) {
        try {
            $this->ApprovedDate = !empty($this->ApprovedDate) ? Yii::$app->formatter->asDatetime($this->ApprovedDate, 'php:Y-m-d H:i:s'): $this->ApprovedDate;
            $this->SentDate = !empty($this->SentDate) ? Yii::$app->formatter->asDatetime($this->SentDate, 'php:Y-m-d H:i:s'): $this->SentDate;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes) {
        try {
            $this->_formatDate();
            $this->IdState ? ($this->state->Code == self::STATE_ACTIVE ? $this->_disabledVersions():NULL):NULL;
        } catch (Exception $ex) {
            throw $ex;
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    private function _formatDate(){
        try {
            $this->ApprovedDate = !empty($this->ApprovedDate) ? Yii::$app->formatter->asDatetime($this->ApprovedDate, 'php:d-m-Y H:i:s'):$this->ApprovedDate;
            $this->SentDate = !empty($this->SentDate) ? Yii::$app->formatter->asDatetime($this->SentDate, 'php:d-m-Y H:i:s'):$this->SentDate;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _disabledVersions(){
        try {
            $versions = self::find()
                    ->joinWith('state b')
                    ->where(['b.Code' => self::STATE_ACTIVE])
                    ->andWhere('policyversion.Id != :id',[':id' => $this->Id])
                    ->all();
            $state = State::findOne(['KeyWord' => StringHelper::basename(self::class),'Code' => self::STATE_OBSOLETE])->Id;
            foreach ($versions as $version){
                $version->IdState = $state;
                $version->save();
            }
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
