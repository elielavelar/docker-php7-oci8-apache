<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Transactionmodel;
/**
 * TransactionmodelSearch represents the model behind the search form of `backend\models\Transactionmodel`.
 */
class TransactionmodelSearch extends Transactionmodel
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdRegistredModel','Enabled'], 'integer'],
            [['KeyWord','NameSpace'], 'safe'],
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
        $_tableName = Transactionmodel::tableName();
        
        $query = Transactionmodel::find()
                ->joinWith('registredModel b');

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
            $_tableName.'.Id' => $this->Id,
            $_tableName.'.IdRegistredModel' => $this->IdRegistredModel,
            $_tableName.'.Enabled' => $this->Enabled,
        ]);

        $query->andFilterWhere(['like', 'b.KeyWord', $this->keyWord])
            ->andFilterWhere(['like', 'b.NameSpace', $this->nameSpace])
            ->andFilterWhere(['like', 'b.AttributeKey', $this->attributeKey]);

        return $dataProvider;
    }
}
