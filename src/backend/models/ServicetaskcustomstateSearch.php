<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Servicetaskcustomstates;

/**
 * ServicetaskcustomstateSearch represents the model behind the search form of `backend\models\Servicetaskcustomstates`.
 */
class ServicetaskcustomstateSearch extends Servicetaskcustomstates
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdServiceTask', 'IdState', 'IdUserCreate', 'IdUserDisabled','Active'], 'integer'],
            [['DateStart', 'DateEnd', 'Description'], 'safe'],
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
        $query = Servicetaskcustomstates::find();

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
            'IdServiceTask' => $this->IdServiceTask,
            'IdState' => $this->IdState,
            'DateStart' => $this->DateStart,
            'DateEnd' => $this->DateEnd,
            'IdUserCreate' => $this->IdUserCreate,
            'IdUserDisabled' => $this->IdUserDisabled,
            'Active' => $this->Active,
        ]);

        $query->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
