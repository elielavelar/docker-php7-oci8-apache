<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use backend\models\InfrastructurerequirementReport;
use backend\models\Infrastructurerequirement;
use backend\models\InfrastructurerequirementSearch;

class InfrastructureRequirementReportController extends Controller
{

    public function actionIndex()
    {
        $model = new InfrastructurerequirementReport();

        $query = Infrastructurerequirement::find();

        $pagination = new Pagination([
            'totalCount' => $query->count(),
        ]);

        $requirements = $query->orderBy('id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        if($model-> load(Yii::$app->request->post()) && $model->validate()){
            return $this->render('report', ['model' => $model, 
                                            'requirements' => $requirements,
                                            'pagination' => $pagination,
                                            ]);
        }else{
            return $this->render('index', ['model' => $model]);
        }        
    }       

    public function actionReport(){
    	$query = Infrastructurerequirement::find();

        $pagination = new Pagination([
            'totalCount' => $query->count(),
        ]);

        $requirements = $query->orderBy('id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('report', [
            'requirements' => $requirements,
            'pagination' => $pagination,
        ]);	
    }

    public function actionList(){
        $query = Infrastructurerequirement::find();

        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);

        $requirements = $query->orderBy('id')
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('list', [
            'requirements' => $requirements,
            'pagination' => $pagination,
        ]);
    }
}