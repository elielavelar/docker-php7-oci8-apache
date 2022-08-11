<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Person;
use common\models\Personaldocument;
/**
 * PersonSearch represents the model behind the search form of `common\models\Person`.
 */
class PersonSearch extends Person
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
            [['Id', 'IdGenderType', 'IdState' ,'IdDocumentType'], 'integer'],
            [['FirstName', 'SecondName', 'ThirdName', 'LastName', 'SecondLastName', 'MarriedName', 'Code', 'DocumentNumber'], 'safe'],
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
        $query = Person::find();

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

        if(!empty($this->DocumentNumber) || !empty($this->IdDocumentType)){
            $query->leftJoin(Personaldocument::tableName().' b', Person::tableName().'.Id = b.IdPerson');
            !empty($this->DocumentNumber) ? $query->andFilterWhere(['b.DocumentNumber' => $this->DocumentNumber]) : null;
            !empty($this->IdDocumentType) ? $query->andFilterWhere(['b.IdDocumentType' => $this->IdDocumentType]) : null;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'Id' => $this->Id,
            'IdGenderType' => $this->IdGenderType,
            'IdState' => $this->IdState,
            'Code' => $this->Code,
        ]);

        $query->andFilterWhere(['like', 'FirstName', $this->FirstName])
            ->andFilterWhere(['like', 'SecondName', $this->SecondName])
            ->andFilterWhere(['like', 'ThirdName', $this->ThirdName])
            ->andFilterWhere(['like', 'LastName', $this->LastName])
            ->andFilterWhere(['like', 'SecondLastName', $this->SecondLastName])
            ->andFilterWhere(['like', 'MarriedName', $this->MarriedName])
                ;

        return $dataProvider;
    }
}
