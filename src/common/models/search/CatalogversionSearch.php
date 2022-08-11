<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Catalogversion;

/**
 * CatalogversionSearch represents the model behind the search form of `common\models\Catalogversions`.
 */
class CatalogversionSearch extends Catalogversion
{
    function __construct() {
        $this->searchModel = true;
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdCatalog', 'IdState', 'CurrentVersion'], 'integer'],
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
        $query = Catalogversion::find();

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
            'IdCatalog' => $this->IdCatalog,
            'IdState' => $this->IdState,
            'CurrentVersion' => $this->CurrentVersion,
        ]);

        $query->andFilterWhere(['like', 'Version', $this->Version])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
