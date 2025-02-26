<?php

namespace backend\controllers;

use Yii;
use common\models\Profile;
use backend\models\ProfileSearch;
use backend\models\ProfileoptionSearch;
use backend\models\Profileoption;
use backend\models\Option;

#use yii\web\Controller;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;

/**
 * ProfileController implements the CRUD actions for Profile model.
 */
class ProfileController extends \yii\web\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Profile models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Profile();
        $searchModel = new ProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Profile model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Profile model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Profile();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->Id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Profile model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        
        $searchModel = new Profileoption();
        $searchModel->list = Profileoption::getHtmlList(['IdProfile'=>$model->Id]);
        
        $modelDetail = new Profileoption();
        $modelDetail->IdProfile = $model->Id;
        
        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post(StringHelper::basename(Profile::class));
            if(isset($post[StringHelper::basename(Profileoption::class)])){
                $model->profilesetting = $post[StringHelper::basename(Profileoption::class)];
            }
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id]);
            } else {
                return $this->render('update', [
                    'model' => $model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail,
                ]);
            }
            
        } else {
            return $this->render('update', [
                'model' => $model, 'searchModel'=>$searchModel, 'modelDetail'=>$modelDetail,
            ]);
        }
    }

    /**
     * Deletes an existing Profile model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Profile model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Profile the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Profile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
