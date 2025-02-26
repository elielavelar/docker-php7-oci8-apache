<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Extendedmodelfieldgroup;

/**
 * ExtendedmodelkeySearch represents the model behind the search form of `common\models\Extendedmodelkeys`.
 */
class ExtendedmodelfieldgroupSearch extends Extendedmodelfieldgroup {
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdExtendedModelKey', 'VisibleContainer','Sort','IdState'], 'integer'],
            [['Name', 'Description'], 'safe'],
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
        $query = Extendedmodelfieldgroup::find();

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
            'IdExtendedModelKey' => $this->IdExtendedModelKey,
            'Sort' => $this->Sort,
            'VisibleContainer' => $this->VisibleContainer,
            'IdState' => $this->IdState,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
