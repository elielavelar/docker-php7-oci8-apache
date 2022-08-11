<?php

namespace common\models;

use Yii;
use common\models\Servicecentre;
use common\models\State;
use common\models\Type;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
use Exception;
use yii\helpers\Json;

/**
 * This is the model class for table "servicetask".
 *
 * @property int $Id
 * @property int $IdService
 * @property string $Name
 * @property string $Host
 * @property string $Route
 * @property int $Port
 * @property int $IdProtocolType
 * @property int $IdState
 * @property int $IdType
 * @property string $Description
 *
 * @property Type $protocolType
 * @property Servicecentreservice $service
 * @property State $state
 * @property Type $type
 * @property Servicetaskcustomstate[] $customStates
 */
class Servicetask extends \yii\db\ActiveRecord
{
    use traits\Servicetasktrait;
    const _FILE_PATH_ = '@backend/web/attachments';
    const _PATH_ATTACHMENTS_ = 'attachments';
    public $errorNo = null;
    public $errorStr = null;
    public $response = [
        'status' => null,
        'name' => null,
        'id' => null,
    ];
    const _STATUS_OK_ = 'OK';
    const _STATUS_ERROR_ = 'ERR';
    const _STATUS_WARNING_ = 'WARN';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicetask';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdService', 'Name', 'Host', 'IdProtocolType', 'IdState', 'IdType'], 'required'],
            [['IdService', 'Port', 'IdProtocolType', 'IdState', 'IdType'], 'integer'],
            [['Description'], 'string'],
            [['Name', 'Host', 'Route'], 'string', 'max' => 100],
            [['IdProtocolType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdProtocolType' => 'Id']],
            [['IdService'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentreservice::className(), 'targetAttribute' => ['IdService' => 'Id']],
            [['IdState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdState' => 'Id']],
            [['IdType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdType' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdService' => 'Id Service',
            'Name' => 'Nombre',
            'Host' => 'Host',
            'Route' => 'Ruta',
            'Port' => 'Puerto',
            'IdProtocolType' => 'Protocolo',
            'IdState' => 'Estado',
            'IdType' => 'Tipo',
            'Description' => 'Descripción',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProtocolType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdProtocolType']);
    }
    
    public function getProtocolTypes(){
        $types = Type::find()
                    ->joinWith('state b')
                    ->where([
                        'b.KeyWord' => StringHelper::basename(Type::class),
                        'b.Code'=> Type::STATUS_ACTIVE,
                        'type.KeyWord' => 'Protocol'.StringHelper::basename(Type::class),
                    ])
                    ->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Servicecentreservice::className(), ['Id' => 'IdService']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdState']);
    }
    
    public function getStates(){
        $states = State::findAll(['KeyWord' => StringHelper::basename(self::class)]);
        return ArrayHelper::map($states, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdType']);
    }
    
    public function getTypes(){
        $types = Type::find()
                    ->joinWith('state b')
                    ->where([
                        'b.KeyWord' => StringHelper::basename(Type::class),
                        'b.Code'=> Type::STATUS_ACTIVE,
                        'type.KeyWord' => StringHelper::basename(self::class),
                    ])
                    ->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustomStates()
    {
        return $this->hasMany(Servicetaskcustomstates::class, ['IdServiceTask' => 'Id']);
    }
    
    public function getServiceStatus(){
        try {
            $response = [];
            switch ($this->type->Code){
                case 'PING':
                    $response = $this->execNmap();
                    break;
                case 'WSP':
                    $response = $this->execWSDLRequest();
                    break;
                case 'HTTP':
                default :
                    $response = $this->execHTTPRequest();
                    break;
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

}
