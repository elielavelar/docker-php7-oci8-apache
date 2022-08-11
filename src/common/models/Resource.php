<?php

namespace common\models;

use backend\models\Incidentresource;
use common\models\traits\Resourcetrait;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "resource".
 *
 * @property int $Id
 * @property int $IdType
 * @property string $Name
 * @property string $Code
 * @property int $IdResourceType
 * @property int $IdServiceCentre
 * @property int $IdState
 * @property string $CreationDate
 * @property int $IdUserCreation
 * @property string|null $LastUpdateDate
 * @property int|null $IdUserLastUpdate
 * @property int|null $IdParent
 * @property string|null $Description
 * @property string $TokenId
 *
 * @property Incidentresource[] $incidentresources
 * @property Servicecentre $serviceCentre
 * @property State $state
 * @property Type $type
 * @property User $userCreation
 * @property Resource $parent
 * @property Resource[] $resources
 * @property Resourcetype $resourceType
 * @property User $userLastUpdate
 */
class Resource extends \yii\db\ActiveRecord
{
    use Resourcetrait;
    const DEFAULT_PREFIX_CODE = 'MB';
    const DEFAULT_TYPE_CODE = 'DFL';
    const DEFAULT_CODE_LENGTH = 10;
    const DEFAULT_CORRELATIVE_LENGTH = 5;
    const STATUS_ACTIVE = 'ACT';
    const STATUS_INACTIVE = 'INA';

    protected $codelenght = 5;
    public $details = [];
    public $query = '';
    public $label = '';
    protected $_isNewRecord = false;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resource';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdType', 'Name', 'IdResourceType', 'IdServiceCentre', 'IdState'], 'required'],
            [['IdType', 'IdResourceType', 'IdServiceCentre', 'IdState', 'IdUserCreation', 'IdUserLastUpdate', 'IdParent']
                , 'integer'],
            [['CreationDate', 'LastUpdateDate'], 'safe'],
            [['Description'], 'string'],
            [['Name'], 'string', 'max' => 100],
            [['Code'], 'string', 'max' => 20],
            [['TokenId'], 'string', 'max' => 64],
            [['Code', 'TokenId'], 'unique'],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentre::class
                , 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::class
                , 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::class
                , 'targetAttribute' => ['IdType' => 'Id']],
            [['IdUserCreation'], 'exist', 'skipOnError' => true, 'targetClass' => User::class
                , 'targetAttribute' => ['IdUserCreation' => 'Id']],
            [['IdParent'], 'exist', 'skipOnError' => true, 'targetClass' => Resource::class
                , 'targetAttribute' => ['IdParent' => 'Id']],
            [['IdResourceType'], 'exist', 'skipOnError' => true, 'targetClass' => Resourcetype::class
                , 'targetAttribute' => ['IdResourceType' => 'Id']],
            [['IdUserLastUpdate'], 'exist', 'skipOnError' => true, 'targetClass' => User::class
                , 'targetAttribute' => ['IdUserLastUpdate' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => Yii::t('system', 'Id'),
            'IdType' => Yii::t('system', 'Type'),
            'Name' => Yii::t('app', 'Name'),
            'Code' => Yii::t('app', 'Code'),
            'TokenId' => Yii::t('app', 'Token'),
            'IdResourceType' => Yii::t('system', 'Resource Type'),
            'IdServiceCentre' => Yii::t('system', 'Servicecentre'),
            'IdState' => Yii::t('system', 'State'),
            'CreationDate' => Yii::t('system', 'Creation Date'),
            'IdUserCreation' => Yii::t('system', 'User Creation'),
            'LastUpdateDate' => Yii::t('system', 'Last Update Date'),
            'IdUserLastUpdate' => Yii::t('system', 'User Last Update'),
            'IdParent' => Yii::t('system', 'Parent'),
            'Description' => Yii::t('system', 'Description'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIncidentresources()
    {
        return $this->hasMany(Incidentresource::class, ['IdResource' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentre::class, ['Id' => 'IdServiceCentre']);
    }

    /**
     * @return array
     */
    public function getServiceCentres(): array
    {
        $centres = Servicecentre::find()
            ->innerJoin( State::tableName().' b', Servicecentre::tableName().'.IdState = b.Id')
            ->select([ Servicecentre::tableName().'.Id', Servicecentre::tableName().'.Name'])
            ->where([
                'b.Code' => Servicecentre::STATE_ACTIVE,
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($centres, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::class, ['Id' => 'IdState']);
    }

    /**
     * @return array
     */
    public function getStates(): array
    {
        $centres = State::find()
            ->select([ 'Id', 'Name'])
            ->where([
                'KeyWord' => StringHelper::basename( self::class ),
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($centres, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::class, ['Id' => 'IdType']);
    }

    /**
     * @return array
     */
    public function getTypes(): array {
        $types = Type::find()
            ->innerJoin( State::tableName(). ' b', Type::tableName().'.IdState = b.Id')
            ->select([Type::tableName().'.Id', Type::tableName().'.Name'])
            ->where([
                'b.Code' => Type::STATUS_ACTIVE,
                Type::tableName().'.KeyWord' => StringHelper::basename( self::class)
            ])->asArray()->all()
        ;
        return ArrayHelper::map($types, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreation()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUserCreation']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Resource::class, ['Id' => 'IdParent']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResources()
    {
        return $this->hasMany(Resource::class, ['IdParent' => 'Id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResourceType()
    {
        return $this->hasOne(Resourcetype::class, ['Id' => 'IdResourceType']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserLastUpdate()
    {
        return $this->hasOne(User::class, ['Id' => 'IdUserLastUpdate']);
    }

    /**
     * @throws Exception
     */
    public function beforeValidate()
    {
        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord){
            $this->_isNewRecord = true;
            $this->IdUserCreation = Yii::$app->getUser()->getIdentity()->Id;
            $this->CreationDate = date('d-m-Y H:i:s');
            $this->_generateCode();
            $this->_generateToken();
        }
        $this->LastUpdateDate = date('d-m-Y H:i:s');
        $this->CreationDate = $this->CreationDate ? Yii::$app->getFormatter()->asDatetime( $this->CreationDate, 'php: Y-m-d H:i:s') : $this->CreationDate;
        $this->LastUpdateDate = $this->LastUpdateDate ? Yii::$app->getFormatter()->asDatetime( $this->LastUpdateDate, 'php: Y-m-d H:i:s') : $this->LastUpdateDate;
        $this->IdUserLastUpdate = Yii::$app->getUser()->getIdentity()->Id;
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        try{
            $this->_isNewRecord ?
                $this->saveResourceApi()
                : null;
        }catch (Exception $exception){
            throw $exception;
        }
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    public function afterFind()
    {
        $this->CreationDate = $this->CreationDate ?
            Yii::$app->getFormatter()->asDatetime( $this->CreationDate, 'php: d-m-Y H:i:s')
            : $this->CreationDate;
        $this->LastUpdateDate = $this->LastUpdateDate ?
            Yii::$app->getFormatter()->asDatetime( $this->LastUpdateDate, 'php: d-m-Y H:i:s')
            : $this->LastUpdateDate;
        $this->label = $this->Id ?
            ($this->Code.' - '.$this->Name)
            : '';
        return parent::afterFind(); // TODO: Change the autogenerated stub
    }

    private function _generateCode (){
        try {
            $prefix = $this->_getPrefixCode() ?: self::DEFAULT_PREFIX_CODE;
            $typeCode = ( $this->IdResourceType ? $this->resourceType->Code : self::DEFAULT_TYPE_CODE);
            $id = (int) $this->_getLastCorrelative() + 1;
            $this->Code = $prefix.$typeCode.str_pad($id, $this->codelenght,'0',STR_PAD_LEFT);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @return string | boolean
    */
    protected function _getPrefixCode(){
        try {
            return Catalogdetail::find()
                ->innerJoin( Catalogversion::tableName().' b', Catalogdetail::tableName().'.IdCatalogVersion = b.Id')
                ->innerJoin( Catalog::tableName().' c', 'b.IdCatalog = c.Id')
                ->select([ Catalogdetail::tableName().'.Code'])
                ->where([
                    'c.KeyWord' => StringHelper::basename( self::class ),
                    'b.CurrentVersion' => Catalogversion::CURRENT_VERSION_ENABLED,
                    Catalogdetail::tableName().'.KeyWord' => ( $this->IdType ? $this->type->Value : null)
                ])->scalar();
        } catch (Exception $exc){
            throw $exc;
        }
    }

    protected function _generateToken(){
        try {
            $this->TokenId = $this->TokenId ?: Yii::$app->customFunctions->getHexString(24);
        } catch (Exception $exc){
            throw $exc;
        }
    }

    protected function _getLastCorrelative() : int {
        try {
            $correlative = 0;
            $code = self::find()->where([
                'IdType' => $this->IdType,
                'IdResourceType' => $this->IdResourceType,
            ])->max('Code');

            if( !empty($code) ){
                $correlative = (int) mb_substr($code, ( mb_strlen($code) - $this->codelenght  ));
            }
            return $correlative;
        } catch (Exception $exc){
            var_dump( $exc->getMessage()); die();
        }
    }

    public function getFilterByAttributes(){
        try {
            $tableName = self::tableName();
            $query = self::find()
                ->select([ "$tableName.Id as id", "CONCAT( $tableName.Code,' - ', $tableName.Name) as text"])

            ->where( array_filter($this->attributes, function( $value ){
                return !empty($value);
            } ));
            if(!empty($this->query)){
                $query->andWhere(['or',
                    ['like',"Name", $this->query],
                    ['like',"Code", $this->query],
                ]);
            }
            return $query->asArray()->all();
        }catch (Exception $exception){
            throw $exception;
        }
    }
}
