<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;

/**
 * UserSearch represents the model behind the search form about `common\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Id', 'CreateDate', 'UpdateDate','IdState','IdProfile','IdServiceCentre'], 'integer'],
            [['Username', 'AuthKey', 'PasswordHash', 'PasswordResetToken', 'Email','DisplayName'], 'safe'],
            [['DisplayName','Username'],'string'],
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
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        
//        $dataProvider->setSort([
//            'attributes' => array_merge($this->attributes(),[
//                'completeName' => [
//                    'asc' => ['FirstName' => SORT_ASC, 'LastName' => SORT_ASC],
//                    'desc' => ['FirstName' => SORT_DESC, 'LastName' => SORT_DESC],
//                    'label' => 'Usuario',
//                    'default' => SORT_ASC
//                ],
//            ]),
//        ]);

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
            'IdProfile' => $this->IdProfile,
            'IdState' => $this->IdState,
            'IdServiceCentre' => $this->IdServiceCentre,
            'Username' => $this->Username,
        ]);

        $query->andFilterWhere(['like', 'DisplayName', $this->DisplayName])
            #->andFilterWhere(['like', 'Username', $this->Username])
            ->andFilterWhere(['like', 'AuthKey', $this->AuthKey])
            ->andFilterWhere(['like', 'PasswordHash', $this->PasswordHash])
            ->andFilterWhere(['like', 'PasswordResetToken', $this->PasswordResetToken])
            ->andFilterWhere(['like', 'Email', $this->Email]);
        
        if(!empty($this->DisplayName)){
            $query->orWhere('user.FirstName LIKE "%' . $this->DisplayName . '%" ' . //This will filter when only first name is searched.
                'OR user.LastName LIKE "%' . $this->DisplayName . '%" '. //This will filter when only last name is searched.
                'OR CONCAT(user.FirstName," ",user.SecondName, " ", user.LastName) LIKE "%' . $this->DisplayName . '%"'. //This will filter when only last name is searched.
                'OR CONCAT(user.SecondName, " ", user.LastName) LIKE "%' . $this->DisplayName . '%"'. //This will filter when only last name is searched.
                'OR CONCAT(user.FirstName, " ", user.LastName) LIKE "%' . $this->DisplayName . '%"' //This will filter when full name is searched.
            );
        }
        return $dataProvider;
    }
}
