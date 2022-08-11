<?php

namespace common\models;

use Yii;
use common\models\Catalogdetail;
use common\models\Catalog;
use kartik\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use Exception;

/**
 * This is the model class for table "personaldocument".
 *
 * @property int $Id
 * @property int $IdPerson
 * @property int $IdDocumentType
 * @property string $DocumentNumber
 *
 * @property Catalogdetail $documentType
 * @property Person $person
 */
class Personaldocument extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personaldocument';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdPerson', 'IdDocumentType', 'DocumentNumber'], 'required'],
            [['IdPerson', 'IdDocumentType'], 'integer'],
            [['DocumentNumber'], 'string', 'max' => 50],
            [['IdPerson', 'IdDocumentType', 'DocumentNumber'], 'unique', 'targetAttribute' => ['IdPerson', 'IdDocumentType', 'DocumentNumber']],
            [['IdDocumentType'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetail::class, 'targetAttribute' => ['IdDocumentType' => 'Id']],
            [['IdPerson'], 'exist', 'skipOnError' => true, 'targetClass' => Person::class, 'targetAttribute' => ['IdPerson' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdPerson' => 'Persona',
            'IdDocumentType' => 'Tipo Documento',
            'DocumentNumber' => 'Número',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasOne(Catalogdetail::class, ['Id' => 'IdDocumentType']);
    }

    public function getDocumentTypes(){
        $types = Catalogdetail::find()
            ->select([Catalogdetail::tableName().'.Id', Catalogdetail::tableName().'.Name'])
            ->joinWith('state b', false)
            ->joinWith('catalogVersion c', false)
            ->innerJoin(Catalog::tableName().' d', 'd.Id = c.IdCatalog')
            ->where([
                'd.KeyWord' => StringHelper::basename(self::class),
                'b.Code' => Type::STATUS_ACTIVE,
            ])
            ->asArray()
            ->all();
        return ArrayHelper::map($types, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerson()
    {
        return $this->hasOne(Person::class, ['Id' => 'IdPerson']);
    }

    public function getAttributeForm(){
        try {

        } catch (Exception $ex){
            throw $ex;
        }
    }
}
