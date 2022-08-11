<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Transaction;

/**
 * TransactionSearch represents the model behind the search form of `backend\models\Transaction`.
 */
class TransactionSearch extends Transaction
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdTransactionModel', 'IdType', 'IdTransactionBatch', 'IdTransaction'], 'integer'],
            [['TransactionKey', 'CreationDate'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Transaction::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'Id' => $this->Id,
            'IdTransactionModel' => $this->IdTransactionModel,
            'IdType' => $this->IdType,
            'IdTransactionBatch' => $this->IdTransactionBatch,
            'CreationDate' => $this->CreationDate,
            'IdTransaction' => $this->IdTransaction,
        ]);

        $query->andFilterWhere(['like', 'TransactionKey', $this->TransactionKey]);

        return $dataProvider;
    }
}
