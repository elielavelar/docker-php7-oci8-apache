<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Registredmodel;

/**
 * RegistredmodelSearch represents the model behind the search form of `common\models\Registredmodel`.
 */
class RegistredmodelSearch extends Registredmodel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'EnableExtended'], 'integer'],
            [['Name', 'KeyWord', 'NameSpace', 'CompletePath', 'Description'], 'safe'],
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
        $query = Registredmodel::find();

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
            'EnableExtended' => $this->EnableExtended,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'KeyWord', $this->KeyWord])
            ->andFilterWhere(['like', 'NameSpace', $this->NameSpace])
            ->andFilterWhere(['like', 'CompletePath', $this->CompletePath])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
