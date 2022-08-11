<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\models\Citizen;

/**
 * CitizenSearch represents the model behind the search form about `frontend\models\Citizen`.
 */
class CitizenSearch extends Citizen
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'IdState'], 'integer'],
            [['Name', 'LastName', 'Email', 'PasswordHash', 'PasswordResetToken', 'AuthKey', 'CreateDate', 'UpdateDate','Telephone'], 'safe'],
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
        $query = Citizen::find();

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
            'CreateDate' => $this->CreateDate,
            'UpdateDate' => $this->UpdateDate,
            'IdState' => $this->IdState,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'LastName', $this->LastName])
            ->andFilterWhere(['like', 'Email', $this->Email])
            ->andFilterWhere(['like', 'PasswordHash', $this->PasswordHash])
            ->andFilterWhere(['like', 'PasswordResetToken', $this->PasswordResetToken])
            ->andFilterWhere(['like', 'AuthKey', $this->AuthKey])
            ->andFilterWhere(['like', 'Telephone', $this->Telephone]);

        return $dataProvider;
    }
}
