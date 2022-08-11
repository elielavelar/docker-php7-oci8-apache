<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Extendedmodelkey;

/**
 * ExtendedmodelkeySearch represents the model behind the search form of `common\models\Extendedmodelkey`.
 */
class ExtendedmodelkeySearch extends Extendedmodelkey
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdExtendedModel', 'EnabledModelSource', 'IdState'], 'integer'],
            [['AttributeKeyName', 'AttributeKeyValue', 'AttributeSourceName', 'Description'], 'safe'],
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
        $query = Extendedmodelkey::find();

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
            'IdExtendedModel' => $this->IdExtendedModel,
            'EnabledModelSource' => $this->EnabledModelSource,
            'IdState' => $this->IdState,
        ]);

        $query->andFilterWhere(['like', 'AttributeKeyName', $this->AttributeKeyName])
            ->andFilterWhere(['like', 'AttributeKeyValue', $this->AttributeKeyValue])
            ->andFilterWhere(['like', 'AttributeSourceName', $this->AttributeSourceName])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
