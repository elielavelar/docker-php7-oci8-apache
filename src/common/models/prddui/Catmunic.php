<?php
namespace common\models\prddui;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "CAT_MUNIC".
 *
 * @property int $COD_DEPTO
 * @property int $COD_MUNIC
 * @property string $NOM_MUNIC
 * @property string $COD_PAIS
 * 
 * @property Catpais $country
 * @property Catdepto $department
 */
class Catmunic extends ActiveRecord {
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.CAT_MUNIC';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_DEPTO', 'COD_MUNIC', 'NOM_MUNIC', 'COD_PAIS'], 'required'],
            [['COD_DEPTO', 'COD_MUNIC'], 'integer'],
            [['NOM_MUNIC'], 'string', 'max' => 50],
            [['COD_PAIS','COD_MUNIC'], 'string', 'max' => 4],
            [['COD_MUNIC'], 'unique', 'targetAttribute' => ['COD_PAIS', 'COD_DEPTO', 'COD_MUNIC'], 'message' => 'Ya existe el Código {value} para el departamento ingresado'],
            [['COD_DEPTO'], 'exist', 'skipOnError' => true, 'targetClass' => Catdepto::class, 'targetAttribute' => ['COD_DEPTO' => 'COD_DEPTO']],
            [['COD_PAIS'], 'exist', 'skipOnError' => true, 'targetClass' => Catpais::class, 'targetAttribute' => ['COD_PAIS' => 'COD_PAIS']],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_DEPTO' => 'Departamento',
            'COD_MUNIC' => 'Municipio',
            'NOM_MUNIC' => 'Nombre',
            'COD_PAIS' => 'País',
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
        return $this->hasOne(Catdepto::class, ['COD_DEPTO' => 'COD_DEPTO']);
    }
}
