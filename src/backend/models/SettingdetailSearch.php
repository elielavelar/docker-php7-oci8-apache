<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Settingsdetail;

/**
 * SettingsSearch represents the model behind the search form about `backend\models\Settings`.
 */
class SettingsdetailSearch extends Settingsdetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'IdSetting' ,'IdState', 'IdType','Sort'], 'integer'],
            [['Name','Code', 'Description','Value'], 'safe'],
            ['Sort','default','value'=>1],
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
        $query = Settingsdetail::find();

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
            'IdSetting' => $this->IdSetting,
            'IdState' => $this->IdState,
            'IdType' => $this->IdType,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Code', $this->Code])
            ->andFilterWhere(['like', 'Value', $this->Value])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
