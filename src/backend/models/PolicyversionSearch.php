<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Policyversions;

/**
 * PolicyversionSearch represents the model behind the search form of `backend\models\Policyversions`.
 */
class PolicyversionSearch extends Policyversions
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdPolicy', 'IdState'], 'integer'],
            [['Version', 'Description'], 'safe'],
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
        $query = Policyversions::find();

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
            'IdPolicy' => $this->IdPolicy,
            'IdState' => $this->IdState,
        ]);

        $query->andFilterWhere(['like', 'Version', $this->Version])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
