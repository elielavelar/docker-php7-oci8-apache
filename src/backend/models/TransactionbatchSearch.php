<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Transactionbatch;

/**
 * TransactionbatchSearch represents the model behind the search form of `backend\models\Transactionbatch`.
 */
class TransactionbatchSearch extends Transactionbatch
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Enabled'], 'integer'],
            [['BatchKey', 'CreationDate', 'CloseDate'], 'safe'],
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
        $query = Transactionbatch::find();

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
            'CreationDate' => $this->CreationDate,
            'CloseDate' => $this->CloseDate,
            'Enabled' => $this->Enabled,
        ]);

        $query->andFilterWhere(['like', 'BatchKey', $this->BatchKey]);

        return $dataProvider;
    }
}
