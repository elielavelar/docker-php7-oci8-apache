<?php

namespace common\models\search;

use common\models\Type;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Equipment;
use yii\helpers\StringHelper;

/**
 * EquipmentSearch represents the model behind the search form of `common\models\Equipment`.
 */
class EquipmentSearch extends Equipment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'IdType', 'IdResourceType', 'IdServiceCentre', 'IdState', 'IdUserCreation', 'IdUserLastUpdate', 'IdParent'], 'integer'],
            [['Name', 'Code', 'CreationDate', 'LastUpdateDate', 'Description', 'TokenId'], 'safe'],
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
        $query = Equipment::find()
            ->innerJoin(Type::tableName().' b', Equipment::tableName().'.IdType = b.Id');

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
            Equipment::tableName().'.Id' => $this->Id,
            Equipment::tableName().'.IdResourceType' => $this->IdResourceType,
            Equipment::tableName().'.IdServiceCentre' => $this->IdServiceCentre,
            Equipment::tableName().'.IdState' => $this->IdState,
            Equipment::tableName().'.CreationDate' => $this->CreationDate,
            Equipment::tableName().'.IdUserCreation' => $this->IdUserCreation,
            Equipment::tableName().'.LastUpdateDate' => $this->LastUpdateDate,
            Equipment::tableName().'.IdUserLastUpdate' => $this->IdUserLastUpdate,
            Equipment::tableName().'.IdParent' => $this->IdParent,
        ]);


        $query->andFilterWhere(['like', Equipment::tableName().'.Name', $this->Name])
            ->andFilterWhere(['like', Equipment::tableName().'.Code', $this->Code])
            ->andFilterWhere(['like', Equipment::tableName().'.Description', $this->Description])
            ->andFilterWhere(['like', Equipment::tableName().'.TokenId', $this->TokenId]);
        $query->andWhere([
            'b.Value' => StringHelper::basename( Equipment::class),
        ]);

        return $dataProvider;
    }
}
