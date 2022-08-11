<?php

namespace backend\models\traits;

use backend\models\Incidentrequest;

use common\customassets\helpers\Html;
use common\models\Attachment;
use kartik\widgets\Select2;
use yii\helpers\StringHelper;

/* @var $this Incidentrequest */
trait Incidentrequesttrait
{

    protected function saveFiles(){
        try {
            $model = new Attachment();
            $model->KeyWord = StringHelper::basename(self::class);
            $model->AttributeName = 'Id';
            $model->AttributeValue = (string) $this->Id;
            $model->fileattachment = $this->fileattachment;
            if(!$model->saveFiles()){
                $message = \Yii::$app->customFunctions->getErrors($model->getErrors());
                throw new \Exception($message, 99000 );
            }
        } catch (\Exception $exception){
            throw $exception;
        }
    }

    public function setSteps(){
        try{
            $model = clone($this);
            $model->setScenario( self::SCENARIO_WIZARD_STEP1);
            $attributes =  $model->activeAttributes();
            $this->steps[] = [
                'model' => clone($model),
                'title' => Html::icon('fas fa-trophy fa-2x'),
                'fieldConfig' => [
                    'IdCategoryType' => [
                        'widget' => Select2::class,
                        'options' => [
                            'data' => $this->getCategoryTypes(),
                            'options' => [
                                'placeholder' => 'Selecciona Tipo'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ]
                    ],
                    'only' => $attributes
                ]
            ];
            $model->setScenario( self::SCENARIO_WIZARD_STEP2);
            $attributes =  $model->activeAttributes();
            $this->steps[] = [
                'model' => clone($model),
                'title' => 'Detalle',
                'fieldConfig' => [
                    'IdServiceCentre' => [
                        'widget' => Select2::class,
                        'options' => [
                            'data' => $this->getServiceCentres(),
                            'options' => [
                                'placeholder' => 'Selecciona Centro de Servicio'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true,
                            ]
                        ]
                    ]
                ],
                'only' => $attributes
            ];
            $this->setScenario( self::SCENARIO_DEFAULT);
        } catch (\Exception $exception){
            throw $exception;
        }
    }
}