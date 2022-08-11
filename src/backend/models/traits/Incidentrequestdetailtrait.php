<?php

namespace backend\models\traits;

use backend\models\Incidentrequestdetail;
use common\models\Attachment;
use yii\helpers\StringHelper;
use yii\helpers\ArrayHelper;
/**
 * @var $this Incidentrequestdetail
 */
trait Incidentrequestdetailtrait
{
    public function getIcon(){
        return ArrayHelper::getValue($this->iconsByActivity, $this->activityType->Code, 'fas fa-info');
    }
    public function getBgClass(){
        return ArrayHelper::getValue($this->classByActivity, $this->activityType->Code, 'gray');
    }

    public function getDescription(){
        return $this->_getDescriptionByType();
    }

    protected function _getDescriptionByType(){
        switch ($this->activityType->Code){
            case self::ACTIVITY_FOLLOWING:
                return $this->_getFollowingDescription();
            case self::ACTIVITY_REASSIGNMENT:
            case self::ACTIVITY_SOLVED:
            case self::ACTIVITY_CLOSE:
                return $this->_getReAssignmentDescription();
            default:
                return [];
        }
    }

    protected function _getAssignmentDescription(): array {
        return [
            [
                'attribute' => 'IdUser',
                'value' => $this->IdUser ? $this->user->DisplayName : null,
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'IdAssignedUser',
                        'value' => $this->IdAssignedUser ? $this->assignedUser->DisplayName : null,
                    ],
                    [
                        'attribute' => 'RecordDate',
                        //'value' => $this->IdAssignedUser ? $this->assignedUser->DisplayName : null,
                    ],
                ]
            ],
        ];
    }

    protected function _getFollowingDescription(): array {
        return [
            [
                'attribute' => 'IdUser',
                'value' => $this->IdUser ? $this->user->DisplayName : null,
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'IdAssignedUser',
                        'value' => $this->IdAssignedUser ? $this->assignedUser->DisplayName : null,
                    ],
                    [
                        'attribute' => 'RecordDate',
                        //'value' => $this->IdAssignedUser ? $this->assignedUser->DisplayName : null,
                    ],
                ]
            ],

            'Description:ntext',
            'Commentaries:ntext',
        ];
    }

    protected function _getReAssignmentDescription(): array {
        return [
            [
                'attribute' => 'IdUser',
                'value' => $this->IdUser ? $this->user->DisplayName : null,
            ],
            [
                'columns' => [
                    [
                        'attribute' => 'IdAssignedUser',
                        'value' => $this->IdAssignedUser ? $this->assignedUser->DisplayName : null,
                    ],
                    [
                        'attribute' => 'RecordDate',
                    ],
                ]
            ],
            'Description:ntext',
        ];
    }


    public static function setAttributesByActivity($code = null){
        try {
            if(empty($code)){
                return self::$initialAttributes;
                //throw new \Exception(\Yii::t('app','{attribute} is required',
                //    ['attribute' => $this->getAttributeLabel('IdActivityType')])
                //);
            }
            $attributes = [];
            switch ($code){
                case self::ACTIVITY_FOLLOWING:
                    $attributes = [
                        'IdActiveType',
                        'IdProblemType',
                        'Commentaries',
                    ];
                    break;
                case self::ACTIVITY_REASSIGNMENT:
                    $attributes = [];
                    break;
                case self::ACTIVITY_SOLVED:
                    $attributes = [
                        'IdActiveType',
                        'IdProblemType',
                    ];
                    break;
            }
            return ArrayHelper::merge(self::$initialAttributes ,$attributes);
        } catch ( \Exception $exception){
            throw $exception;
        }
    }

    public function getAttributesByActivity(){
        try {
            $this->attributesByActivity = self::setAttributesByActivity( ($this->IdActivityType ? $this->activityType->Code : null) );
            return ArrayHelper::filter(ArrayHelper::merge($this->attributes, ['fileattachment' => '']), $this->attributesByActivity);
        }  catch ( \Exception $exception){
            throw $exception;
        }
    }

    public function getAttributesView(){
        try {
            $displayValues = [
                'Id',
                [
                    'attribute' => 'IdIncident',
                    'value' => $this->IdIncident ? $this->incident->Ticket : '',
                ],
                [
                    'attribute' => 'IdActivityType',
                    'value' => $this->IdActivityType ? $this->activityType->Name : '',
                ],
                'Description',
                'DetailDate',
                'RecordDate',
                [
                    'attribute' => 'IdIncidentRequestState',
                    'value' => $this->IdIncidentRequestState ? $this->incidentRequestState->Name : '',
                ],
                [
                    'attribute' => 'IdAssignedUser',
                    'value' => $this->IdAssignedUser ? $this->assignedUser->DisplayName : '',
                ],
                [
                    'attribute' => 'IdUser',
                    'value' => $this->IdUser ? $this->user->DisplayName : '',
                ],
                'Commentaries',
            ];
            $attributes = [
            ];
            $this->getAttributesByActivity();
            $activityValues = $this->attributesByActivity;
            $activityValues = array_merge($activityValues, ['IdUser', 'IdAssignedUser']);

            foreach ($displayValues as $attribute => $value){
                switch( gettype($attribute)){
                    case 'array':
                        in_array(ArrayHelper::getValue($attribute, 'attribute'), $activityValues)
                            ? array_push($attributes, $attribute)
                            : null;
                        break;
                    default:
                        in_array($value, $activityValues)
                            ? array_push($attributes, $value)
                            : null ;
                }
            }
            return $attributes;
        } catch ( \Exception $exception){
            throw $exception;
        }
    }

    protected function saveFiles(){
        try {
            $model = new Attachment();
            $model->KeyWord = StringHelper::basename(self::class);
            $model->AttributeName = 'Id';
            $model->AttributeValue = (string) $this->Id;
            $model->fileattachment = $this->fileattachment;
            if(!$model->saveFiles()){
                $message = \Yii::$app->customFunctions->getErrors($model->getErrors());
                \Yii::$app->appLog->setLog($message, 'error');
                throw new \Exception($message, 99000 );
            }
        } catch (\Exception $exception){
            throw $exception;
        }
    }
}