<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Typefields;

/**
 * TypefieldSearch represents the model behind the search form of `backend\models\Typefields`.
 */
class TypefieldSearch extends Typefields
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdType', 'IdField', 'IdState', 'Required', 'Sort', 'ColSpan', 'RowSpan'], 'integer'],
            [['CustomLabel', 'CssClass', 'Description'], 'safe'],
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
        $query = Typefields::find();

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
            'IdField' => $this->IdField,
            'IdState' => $this->IdState,
            'Required' => $this->Required,
            'Sort' => $this->Sort,
            'ColSpan' => $this->ColSpan,
            'RowSpan' => $this->RowSpan,
        ]);

        $query->andFilterWhere(['like', 'CustomLabel', $this->CustomLabel])
            ->andFilterWhere(['like', 'CssClass', $this->CssClass])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
