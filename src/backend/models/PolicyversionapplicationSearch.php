<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Policyversionapplication;

/**
 * PolicyversionapplicationSearch represents the model behind the search form of `backend\models\Policyversionapplication`.
 */
class PolicyversionapplicationSearch extends Policyversionapplication
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdPolicyVersion', 'IdCatalogDetail', 'IdRecord'], 'integer'],
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
        $query = Policyversionapplication::find();

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
            'IdPolicyVersion' => $this->IdPolicyVersion,
            'IdCatalogDetail' => $this->IdCatalogDetail,
            'IdRecord' => $this->IdRecord,
        ]);

        return $dataProvider;
    }
}
