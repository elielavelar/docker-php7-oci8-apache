<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Clientuser;

/**
 * ClientuserSearch represents the model behind the search form of `common\models\Clientuser`.
 */
class ClientuserSearch extends Clientuser
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdState'], 'integer'],
            [['AuthKey', 'ShortCode', 'PasswordHash', 'PasswordResetToken', 'Email', 'CreateDate', 'UpdateDate', 'FirstName', 'SecondName', 'LastName', 'SecondLastName', 'PasswordExpirationDate', 'SecondEmail', 'TempToken'], 'safe'],
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
        $query = Clientuser::find();

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
            'PasswordExpirationDate' => $this->PasswordExpirationDate,
        ]);

        $query->andFilterWhere(['like', 'AuthKey', $this->AuthKey])
            ->andFilterWhere(['like', 'ShortCode', $this->ShortCode])
            ->andFilterWhere(['like', 'PasswordHash', $this->PasswordHash])
            ->andFilterWhere(['like', 'PasswordResetToken', $this->PasswordResetToken])
            ->andFilterWhere(['like', 'Email', $this->Email])
            ->andFilterWhere(['like', 'FirstName', $this->FirstName])
            ->andFilterWhere(['like', 'SecondName', $this->SecondName])
            ->andFilterWhere(['like', 'LastName', $this->LastName])
            ->andFilterWhere(['like', 'SecondLastName', $this->SecondLastName])
            ->andFilterWhere(['like', 'SecondEmail', $this->SecondEmail])
            ->andFilterWhere(['like', 'TempToken', $this->TempToken]);

        return $dataProvider;
    }
}
