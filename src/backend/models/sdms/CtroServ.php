<?php

namespace backend\models\sdms;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use common\models\prddui\Catpais;
use common\models\prddui\Catdepto;
use common\models\prddui\Catmunic;

/**
 * This is the model class for table "COD_CTRO_SERV".
 *
 * @property int $COD_CTRO_SERV
 * @property int $COD_DEPTO_CTRO
 * @property int $COD_MUNIC_CTRO
 * @property string $DESC_CTRO_SERV
 * @property string $TEL_CTRO_SERV
 * @property string $CTRO_MOV
 * @property string $DIRECCION Direccion del centro de servicio
 * @property string $COD_PAIS Codigo de pais del Duicentro
 * @property string $COD_HHORARIO Codigo de Zona Horaria para almacenar hora
 * 
 * @property Catpais $country
 * @property Catdepto $department
 * @property Catmunic $municipality
 */

class CtroServ extends ActiveRecord {
    
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.CTRO_SERV';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_CTRO_SERV', 'DESC_CTRO_SERV'], 'required'],
            [['COD_CTRO_SERV','COD_DEPTO_CTRO','COD_MUNIC_CTRO'], 'integer'],
            [['COD_PAIS'], 'string', 'max' => 4],
            [['DESC_CTRO_SERV'], 'string', 'max' => 40],
            [['DIRECCION'], 'string', 'max' => 50],
            [['TEL_CTRO_SERV'], 'string', 'max' => 20],
            [['COD_HHORARIO'], 'string', 'max' => 5],
            [['CTRO_MOV'], 'string', 'max' => 1],
            [['COD_CTRO_SERV'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_CTRO_SERV' => 'Duicentro',
            'COD_DEPTO_CTRO' => 'Departamento',
            'COD_MUNIC_CTRO' => 'Municipio',
            'DESC_CTRO_SERV' => 'Nombre',
            'TEL_CTRO_SERV' => 'Teléfono',
            'CTRO_MOV' => 'Centro Movil',
            'DIRECCION' => 'Dirección',
            'COD_PAIS' => 'País',
            'COD_HHORARIO' => 'Horario',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(Catpais::class, ['COD_PAIS' => 'COD_PAIS']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Catdepto::class, ['COD_DEPTO' => 'COD_DEPTO_CTRO', 'COD_PAIS' => 'COD_PAIS']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipality()
    {
        return $this->hasOne(Catmunic::class, ['COD_MUNIC' => 'COD_MUNIC_CTRO', 'COD_DEPTO' => 'COD_DEPTO_CTRO', 'COD_PAIS' => 'COD_PAIS']);
    }
}
