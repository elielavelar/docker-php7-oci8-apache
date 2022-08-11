<?php

namespace backend\models\sdms;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\sdms\DatosOper;

/**
 * DatosoperSearch represents the model behind the search form of `backend\models\DatosOper`.
 */
class DatosoperSearch extends DatosOper
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['COD_OPER', 'PASWD_SISTEMA', 'PASWD_RED', 'NOM1_OPER', 'NOM2_OPER'
                , 'NOM3_OPER', 'APDO1_OPER', 'APDO2_OPER', 'STAT_OPER', 'COD_CARGO_OPER' ,'COD_CTRO_SERV', 'COD_EMPLEADO', 'FECHA_CAMBIO','nameOper'], 'safe'],
            [['COD_ROL', 'COD_CARGO_OPER'], 'integer'],
            [['nameOper'],'string'],
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
        $query = DatosOper::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
        $dataProvider->setSort([
            'attributes' => array_merge($this->attributes(),[
                'nameOper' => [
                    'asc' => ['NOM1_OPER' => SORT_ASC, 'APDO1_OPER' => SORT_ASC],
                    'desc' => ['NOM1_OPER' => SORT_DESC, 'APDO1_OPER' => SORT_DESC],
                    'label' => 'Nombre',
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
            'COD_ROL' => $this->COD_ROL,
            'COD_CARGO_OPER' => $this->COD_CARGO_OPER,
            'STAT_OPER' => $this->STAT_OPER,
            'COD_CTRO_SERV' => $this->COD_CTRO_SERV,
        ]);

        $query->andFilterWhere(['like', 'COD_OPER', $this->COD_OPER])
            ->andFilterWhere(['like', 'PASWD_SISTEMA', $this->PASWD_SISTEMA])
            ->andFilterWhere(['like', 'PASWD_RED', $this->PASWD_RED])
            ->andFilterWhere(['like', 'NOM1_OPER', $this->NOM1_OPER])
            ->andFilterWhere(['like', 'NOM2_OPER', $this->NOM2_OPER])
            ->andFilterWhere(['like', 'NOM3_OPER', $this->NOM3_OPER])
            ->andFilterWhere(['like', 'APDO1_OPER', $this->APDO1_OPER])
            ->andFilterWhere(['like', 'APDO2_OPER', $this->APDO2_OPER])
            ->andFilterWhere(['like', 'COD_EMPLEADO', $this->COD_EMPLEADO])
            ->andFilterWhere(['like', 'FECHA_CAMBIO', $this->FECHA_CAMBIO]);
        
        $query->andWhere("NOM1_OPER LIKE '%" . $this->nameOper . "%' " . //This will filter when only first name is searched.
            "OR APDO1_OPER LIKE '%" . $this->nameOper . "%' ". //This will filter when only last name is searched.
            "OR (NOM1_OPER||' '||APDO1_OPER) LIKE '%" . $this->nameOper. "%'" //This will filter when full name is searched.
        );

        return $dataProvider;
    }
}
