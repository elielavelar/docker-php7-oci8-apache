<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Field;

/**
 * FieldsSearch represents the model behind the search form of `common\models\Fields`.
 */
class FieldSearch extends Field
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdType', 'IdState', 'HasCatalog', 'UseMask', 'MultipleValue'], 'integer'],
            [['Name', 'KeyWord', 'Code', 'Description', 'Value', 'DefaultMask'], 'safe'],
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
        $query = Field::find();

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
            'IdType' => $this->IdType,
            'IdState' => $this->IdState,
            'HasCatalog' => $this->HasCatalog,
            'UseMask' => $this->UseMask,
            'MultipleValue' => $this->MultipleValue,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'KeyWord', $this->KeyWord])
            ->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Description', $this->Description])
            ->andFilterWhere(['like', 'DefaultMask', $this->DefaultMask])
            ->andFilterWhere(['like', 'Value', $this->Value]);

        return $dataProvider;
    }
}
