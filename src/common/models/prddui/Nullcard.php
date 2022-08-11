<?php
namespace common\models\prddui;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use Exception;


/**
 * This is the model class for table "DETALLE_TARJETAS_ANULADAS".
 *
 * @property int $COD_CTRO_SERV
 * @property string $FECHA
 * @property string $COD_CONS
 * @property string $COD_DEVOLUCION
 * @property int $CORRELATIVO
 * @property int $NUMERO_MOVIMIENTO
 * @property string $OPERADOR
 * @property int $NUMERO_TARJETA
 * @property string $IMPRESORA
 * @property int $SOLICITUD
 * @property string $COD_TARJETA
 */

class Nullcard extends ActiveRecord {
    //put your code here
    
    public static function getDb() {
        return Yii::$app->prddui;
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'PRDDUI.DETALLE_TARJETAS_ANULADAS';
    }
    
    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'FECHA',
                ],
                'value' => function ($event) {
                    return new Expression("TO_DATE('" . $this->FECHA . "','YYYY-MM-DD')");
                },
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_CTRO_SERV', 'FECHA', 'COD_CONS', 'COD_DEVOLUCION', 'CORRELATIVO', 'NUMERO_MOVIMIENTO', 'OPERADOR', 'NUMERO_TARJETA', 'IMPRESORA'], 'required'],
            [['COD_CTRO_SERV','CORRELATIVO', 'NUMERO_MOVIMIENTO', 'NUMERO_TARJETA', 'SOLICITUD'], 'integer'],
            [['FECHA'], 'string'],
            [['COD_CONS'], 'string','max' => 10],
            [['COD_DEVOLUCION', 'OPERADOR'], 'string','max' => 8],
            [['IMPRESORA'], 'string', 'max' => 20],
            [['COD_TARJETA'], 'string', 'max' => 2],
            [['COD_CTRO_SERV', 'FECHA', 'CORRELATIVO'], 'unique'],
            [['COD_CTRO_SERV', 'FECHA', 'NUMERO_TARJETA'], 'unique'],
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'COD_CTRO_SERV' => 'Duicentro',
            'FECHA' => 'Fecha',
            'COD_CONS' => 'Cod. Cons',
            'COD_DEVOLUCION'  => 'Código Devolución',
            'CORRELATIVO' => 'Correlativo',
            'NUMERO_MOVIMIENTO'  => 'Número Movimiento',
            'OPERADOR' => 'Operador',
            'NUMERO_TARJETA' => 'Número Tarjeta',
            'IMPRESORA' => 'Impresora',
            'SOLICITUD' => 'Solicitud',
            'COD_TARJETA' => 'Código Tarjeta',
        ];
    }
    
    public function afterFind() {
        if(!empty($this->FECHA)){
            $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->FECHA);
            $this->FECHA = $date->format('d-m-Y');
        }
        return parent::afterFind();
    }

   
}
