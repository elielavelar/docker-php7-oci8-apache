<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Attachment;

/**
 * AttachmentSearch represents the model behind the search form of `backend\models\Attachments`.
 */
class AttachmentSearch extends Attachment
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
        $query = Attachment::find();

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
            'AttributeName' => $this->AttributeName,
            'AttributeValue' => $this->AttributeValue,
            'IdUser' => $this->IdUser,
        ]);

        $query->andFilterWhere(['like', 'FileName', $this->FileName])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
