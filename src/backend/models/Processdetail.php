<?php

namespace backend\models;

use Yii;
use common\models\Servicecentre;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "processdetail".
 *
 * @property int $Id
 * @property int $IdProcess
 * @property int $IdServiceCentre
 * @property int $Enabled
 *
 * @property Process $process
 * @property Servicecentre $serviceCentre
 */
class Processdetail extends \yii\db\ActiveRecord
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'processdetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdProcess', 'IdServiceCentre'], 'required'],
            [['IdProcess', 'IdServiceCentre', 'Enabled'], 'integer'],
            [['IdProcess'], 'exist', 'skipOnError' => true, 'targetClass' => Process::className(), 'targetAttribute' => ['IdProcess' => 'Id']],
            [['IdServiceCentre'], 'exist', 'skipOnError' => true, 'targetClass' => Servicecentres::className(), 'targetAttribute' => ['IdServiceCentre' => 'Id']],
            ['Enabled','default','value'=> self::STATUS_ENABLED],
            ['Enabled', 'in', 'range' => [self::STATUS_DISABLED, self::STATUS_ENABLED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdProcess' => 'Proceso',
            'IdServiceCentre' => 'Departamento',
            'Enabled' => 'Habilitado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProcess()
    {
        return $this->hasOne(Process::className(), ['Id' => 'IdProcess']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCentre()
    {
        return $this->hasOne(Servicecentres::className(), ['Id' => 'IdServiceCentre']);
    }
    
    public function getServiceCentres(){
        try {
            $droptions = Servicecentres::find()
                    ->joinWith('state b')
                    ->joinWith('type c')
                    ->where([
                        'b.KeyWord' => StringHelper::basename(Servicecentres::class),
                        'b.Code' => StringHelper::basename(Servicecentres::STATE_ACTIVE),
                        'c.KeyWord' => StringHelper::basename(Servicecentres::class),
                    ])
                    ->andWhere('c.Code != :code',[':code' => Servicecentres::TYPE_DUISITE])
                    ->all();
            return ArrayHelper::map($droptions, 'Id', 'Name');
        } catch (Exception $ex) {
            throw $ex;
        }
    }
}
