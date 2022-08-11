<?php
namespace common\models\prddui;

use Yii;
use yii\db\ActiveRecord;
/**
 * This is the model class for table "CAT_PAIS".
 *
 * @property int $COD_PAIS
 * @property string $NOM_PAIS
 * @property string $ABREV
 * @property int $NUMERO
 * 
 * @property Catdepto[] $departments
 */
class Catpais extends ActiveRecord {
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.CAT_PAIS';
    }
    
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_PAIS', 'NOM_PAIS', 'NUMERO'], 'required'],
            [['NUMERO'], 'integer'],
            [['NOM_PAIS'], 'string', 'max' => 50],
            [['COD_PAIS'], 'string', 'max' => 2],
            [['ABREV'], 'string', 'max' => 10],
            [['COD_PAIS','NUMERO','ABREV'], 'unique'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_PAIS' => 'País',
            'NOM_PAIS' => 'Nombre',
            'ABREV' => 'Abreviatura',
            'NUMERO' => 'Número',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartments()
    {
        return $this->hasMany(Catdepto::class, ['COD_PAIS' => 'COD_PAIS']);
    }
    
}
