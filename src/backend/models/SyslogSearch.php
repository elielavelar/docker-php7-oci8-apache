<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Syslog;

/**
 * SyslogSearch represents the model behind the search form of `backend\models\Syslog`.
 */
class SyslogSearch extends Syslog
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdTransactionModel', 'IdRecord', 'IdUser'], 'integer'],
            [['LogKey', 'Title', 'ActionType', 'CreationDate', 'ControllerName', 'ActionName', 'EnvironmentName', 'Description','userName'], 'safe'],
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
        $query = Syslog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [ 'pageSize' => 10 ],
        ]);
        
        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(),[
                'userName' => [
                    'asc' => ['DisplayName' => SORT_ASC],
                    'desc' => ['DisplayName' => SORT_DESC],
                    'label' => 'Usuario',
                    'default' => SORT_ASC
                ],
            ]),
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
            'IdTransactionModel' => $this->IdTransactionModel,
            'IdRecord' => $this->IdRecord,
            'CreationDate' => $this->CreationDate,
            'IdUser' => $this->IdUser,
        ]);

        $query->andFilterWhere(['like', 'LogKey', $this->LogKey])
            ->andFilterWhere(['like', 'Title', $this->Title])
            ->andFilterWhere(['like', 'ActionType', $this->ActionType])
            ->andFilterWhere(['like', 'ControllerName', $this->ControllerName])
            ->andFilterWhere(['like', 'userName', $this->userName])
            ->andFilterWhere(['like', 'ActionName', $this->ActionName])
            ->andFilterWhere(['like', 'EnvironmentName', $this->EnvironmentName])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
