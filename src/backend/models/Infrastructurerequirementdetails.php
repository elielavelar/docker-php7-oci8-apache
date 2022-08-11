<?php

namespace backend\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;
use common\models\Type;
use common\models\State;
use common\models\User;
use common\models\Catalogdetailvalues;
use backend\models\Infrastructurerequirement;
use Exception;

/**
 * This is the model class for table "infrastructurerequirementdetails".
 *
 * @property int $Id
 * @property int $IdInfrastructureRequirement
 * @property string $Title
 * @property string $Description
 * @property string $DetailDate
 * @property string $RecordDate
 * @property string $SolutionDate
 * @property int $IdUser
 * @property int $IdActivityType
 * @property int $IdRequirementState
 * @property int $IdAssignedUser
 * @property string $Commentaries
 * @property int $IdCatalogDetailValue
 *
 * @property Type $activityType
 * @property User $assignedUser
 * @property Catalogdetailvalues $catalogDetailValue
 * @property State $requirementState
 * @property Infrastructurerequirement $infrastructureRequirement
 * @property User $user
 */
class Infrastructurerequirementdetails extends \yii\db\ActiveRecord
{
    
    const ACTIVITY_ASSIGNMENT = 'ASG';
    const ACTIVITY_FOLLOWING = 'FLLW';
    const ACTIVITY_REASSIGNMENT = 'RASG';
    const ACTIVITY_CLOSE = 'CLS';
    const ACTIVITY_EVALUATION = 'EVAL';
    const ACTIVITY_CANCEL = 'CNCL';
    
    const ASSIGNMENT_PROFILES = 'ASG';
    private $activityState = [];
    
    function __construct($config = array()) {
         $this->activityState = [
            self::ACTIVITY_ASSIGNMENT => Infrastructurerequirement::STATE_PENDENT,
            self::ACTIVITY_FOLLOWING => Infrastructurerequirement::STATE_INPROCESS,
            self::ACTIVITY_REASSIGNMENT => Infrastructurerequirement::STATE_PENDENT,
            self::ACTIVITY_EVALUATION => Infrastructurerequirement::STATE_CLOSED,
            self::ACTIVITY_CLOSE => Infrastructurerequirement::STATE_REPAIRED,
            self::ACTIVITY_CANCEL => Infrastructurerequirement::STATE_UNREPAIRED,
        ];
        return parent::__construct($config);
    }
    
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'infrastructurerequirementdetails';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['IdInfrastructureRequirement', 'Title', 'IdUser', 'IdActivityType', 'IdRequirementState', 'IdAssignedUser'], 'required'],
            [['IdInfrastructureRequirement', 'IdUser', 'IdActivityType', 'IdRequirementState', 'IdAssignedUser', 'IdCatalogDetailValue'], 'integer'],
            [['Description', 'Commentaries'], 'string'],
            [['DetailDate', 'RecordDate', 'SolutionDate'], 'safe'],
            [['Title'], 'string', 'max' => 250],
            [['IdActivityType'], 'exist', 'skipOnError' => true, 'targetClass' => Type::className(), 'targetAttribute' => ['IdActivityType' => 'Id']],
            [['IdAssignedUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdAssignedUser' => 'Id']],
            [['IdCatalogDetailValue'], 'exist', 'skipOnError' => true, 'targetClass' => Catalogdetailvalues::className(), 'targetAttribute' => ['IdCatalogDetailValue' => 'Id']],
            [['IdRequirementState'], 'exist', 'skipOnError' => true, 'targetClass' => State::className(), 'targetAttribute' => ['IdRequirementState' => 'Id']],
            [['IdInfrastructureRequirement'], 'exist', 'skipOnError' => true, 'targetClass' => Infrastructurerequirement::className(), 'targetAttribute' => ['IdInfrastructureRequirement' => 'Id']],
            [['IdUser'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['IdUser' => 'Id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'Id' => 'ID',
            'IdInfrastructureRequirement' => 'Id Infrastructure Requirement',
            'Title' => 'Title',
            'Description' => 'Description',
            'DetailDate' => 'Detail Date',
            'RecordDate' => 'Record Date',
            'SolutionDate' => 'Solution Date',
            'IdUser' => 'Id User',
            'IdActivityType' => 'Id Activity Type',
            'IdRequirementState' => 'Id Requirement State',
            'IdAssignedUser' => 'Id Assigned User',
            'Commentaries' => 'Commentaries',
            'IdCatalogDetailValue' => 'Id Catalog Detail Value',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getActivityType()
    {
        return $this->hasOne(Type::className(), ['Id' => 'IdActivityType']);
    }
    
    public function getActivityTypes()
    {
        $model = Type::find()
                ->joinWith('state b')
                ->where([
                    'b.Code' => Type::STATUS_ACTIVE,
                    'type.KeyWord' => StringHelper::basename(self::class).'Activity',
                ])
                ->all();
        return ArrayHelper::map($model, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAssignedUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdAssignedUser']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogDetailValue()
    {
        return $this->hasOne(Catalogdetailvalues::className(), ['Id' => 'IdCatalogDetailValue']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRequirementState()
    {
        return $this->hasOne(State::className(), ['Id' => 'IdRequirementState']);
    }
    
    public function getRequirementStates()
    {
        $model = State::findAll(['KeyWord' => StringHelper::basename(Infrastructurerequirement::class)]);
        return ArrayHelper::map($model, 'Id', 'Name');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInfrastructureRequirement()
    {
        return $this->hasOne(Infrastructurerequirement::className(), ['Id' => 'IdInfrastructureRequirement']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['Id' => 'IdUser']);
    }
}
