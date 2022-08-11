<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Attachments;

/**
 * AttachmentSearch represents the model behind the search form of `backend\models\Attachments`.
 */
class AttachmentSearch extends Attachments
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdUser'], 'integer'],
            [['AttributeName', 'KeyWord', 'AttributeValue', 'FileName', 'Description', 'CreationDate'], 'safe'],
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
        $query = Attachments::find();

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
            'KeyWord' => $this->KeyWord,
            'CreationDate' => $this->CreationDate,
            'IdUser' => $this->IdUser,
        ]);

        $query->andFilterWhere(['like', 'AttributeName', $this->AttributeName])
            ->andFilterWhere(['like', 'AttributeValue', $this->AttributeValue])
            ->andFilterWhere(['like', 'FileName', $this->FileName])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
