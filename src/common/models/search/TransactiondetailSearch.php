<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Transactiondetail;

/**
 * TransactiondetailSearch represents the model behind the search form of `backend\models\Transactiondetail`.
 */
class TransactiondetailSearch extends Transactiondetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdTransaction'], 'integer'],
            [['Attribute', 'Value'], 'safe'],
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
        $query = Transactiondetail::find();

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
            'IdTransaction' => $this->IdTransaction,
        ]);

        $query->andFilterWhere(['like', 'Attribute', $this->Attribute])
            ->andFilterWhere(['like', 'Value', $this->Value]);

        return $dataProvider;
    }
}
