<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "transactiondetail".
 *
 * @property int $Id
 * @property string $IdTransaction
 * @property string $Attribute
 * @property string $Value
 *
 * @property Transaction $transaction
 */
class Transactiondetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transactiondetail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdTransaction', 'Attribute'], 'required'],
            [['IdTransaction'], 'integer'],
            [['Value'], 'string'],
            [['Attribute'], 'string', 'max' => 200],
            [['IdTransaction'], 'exist', 'skipOnError' => true, 'targetClass' => Transaction::class, 'targetAttribute' => ['IdTransaction' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdTransaction' => 'Id Transaction',
            'Attribute' => 'Attribute',
            'Value' => 'Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransaction()
    {
        return $this->hasOne(Transaction::class, ['Id' => 'IdTransaction']);
    }
}
