<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Registredmodelkey;

/**
 * RegistredmodelkeySearch represents the model behind the search form of `common\models\Registredmodelkey`.
 */
class RegistredmodelkeySearch extends Registredmodelkey
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdRegistredModel'], 'integer'],
            [['AttributeKeyName', 'Description'], 'safe'],
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
        $query = Registredmodelkey::find();

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
            'IdRegistredModel' => $this->IdRegistredModel,
        ]);

        $query->andFilterWhere(['like', 'AttributeKeyName', $this->AttributeKeyName])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
