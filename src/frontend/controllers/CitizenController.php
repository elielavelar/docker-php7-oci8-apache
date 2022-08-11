<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use frontend\models\Citizen;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use Exception;
#use yii\helpers\Json;

class CitizenController extends Controller
{
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
    
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionProfile()
    {
        if($this->validateUser()){
            $model = $this->findModel(\Yii::$app->user->getIdentity()->getId());
            return $this->render('profile', [
                'model' => $model,
            ]);
        }
    }
    
    /**
     * Updates an existing Citizen model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate()
    {
        if($this->validateUser()){
            $user =  \Yii::$app->user->getIdentity();
            $id = \Yii::$app->user->getIdentity()->getId();
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['profile', 'id' => $model->Id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }
    
    public function actionConfirm(){
        $response = [];
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('data');
                $data = json_decode($data, TRUE);
                $id = \Yii::$app->user->getIdentity()->getId();
                $model = Citizen::findOne(['Id'=>$id,'ShortCode'=>$data["ShortCode"]]);
                if($model == NULL){
                    throw new Exception('No se encontró Usuario', 90001);
                }
                $response = $model->activate();
            }
        } catch (Exception $ex) {
            $response = [
                'success'=> FALSE,
                'message'=> $ex->getMessage(),
                'code'=> $ex->getCode(),
            ];
        }
        return $response;
    }

    protected function findModel($id)
    {
        if (($model = Citizen::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
//    public function actionSendConfirmationMail(){
//        try {
//            $model = $this->findModel(\Yii::$app->user->getIdentity()->getId());
//            $response = $model->sendEmailConfirmation();
//        } catch (Exception $exc) {
//            $response = [
//                'success'=>FALSE,
//                'message'=>$exc->getMessage(),
//                'code'=>$exc->getCode(),
//            ];
//        }
//        echo Json::encode($response);
//    }
    
    public function actionMail(){
        $model = $this->findModel(\Yii::$app->user->getIdentity()->getId());
        $model->sendEmailConfirmation();
        #            $this->goBack();
        return $this->redirect(['profile']);
    }
    
    private function validateUser($id = NULL){
        if(Yii::$app->user->isGuest){
            Yii::$app->getSession()->setFlash('error','Usuario sin Sesión');
            $this->redirect(['site/login']);
        } else {
            $user = Yii::$app->user->getIdentity();
            if($id != NULL && $id != $user->getId()){
                Yii::$app->getSession()->setFlash('error','Credenciales de usuario no coinciden!');
                $this->goHome();
            } else {
                return TRUE;
            }
        }
    }
    
    
}
