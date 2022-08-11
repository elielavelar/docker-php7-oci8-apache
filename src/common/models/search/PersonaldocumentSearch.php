<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Personaldocument;

/**
 * PersonaldocumentSearch represents the model behind the search form of `common\models\Personaldocument`.
 */
class PersonaldocumentSearch extends Personaldocument
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdPerson', 'IdDocumentType'], 'integer'],
            [['DocumentNumber'], 'safe'],
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
        $query = Personaldocument::find();

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
            'IdPerson' => $this->IdPerson,
            'IdDocumentType' => $this->IdDocumentType,
        ]);

        $query->andFilterWhere(['like', 'DocumentNumber', $this->DocumentNumber]);

        return $dataProvider;
    }
}
