<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Servicetask;

/**
 * ServicetaskSearch represents the model behind the search form of `backend\models\Servicetask`.
 */
class ServicetaskSearch extends Servicetask
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdService', 'Port', 'IdProtocolType', 'IdState', 'IdType'], 'integer'],
            [['Name', 'Host', 'Route', 'Description'], 'safe'],
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
        $query = Servicetask::find();

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
            'IdService' => $this->IdService,
            'Port' => $this->Port,
            'IdProtocolType' => $this->IdProtocolType,
            'IdState' => $this->IdState,
            'IdType' => $this->IdType,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Host', $this->Host])
            ->andFilterWhere(['like', 'Route', $this->Route])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
