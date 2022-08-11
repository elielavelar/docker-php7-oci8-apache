<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Servicecentre;

/**
 * ServicecentresSearch represents the model behind the search form about `common\models\Servicecentres`.
 */
class ServicecentresSearch extends Servicecentre
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'MBCode', 'IdCountry', 'IdState', 'IdType','IdZone'], 'integer'],
            [['Name','ShortName', 'Address', 'Phone'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Servicecentre::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
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
            'MBCode' => $this->MBCode,
            'IdCountry' => $this->IdCountry,
            'IdState' => $this->IdState,
            'IdType' => $this->IdType,
            'IdZone' => $this->IdZone,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'ShortName', $this->ShortName])
            ->andFilterWhere(['like', 'Address', $this->Address])
            ->andFilterWhere(['like', 'Phone', $this->Phone]);

        return $dataProvider;
    }
}
