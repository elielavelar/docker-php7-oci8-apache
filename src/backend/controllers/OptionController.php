<?php

namespace backend\controllers;

use Yii;
use backend\models\Option;
use backend\models\OptionSearch;
use backend\models\Optionenvironment;
use backend\controllers\CustomController;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\StringHelper;
use yii\web\Response;
use yii\helpers\Json;
use Exception;

use common\models\Type;

/**
 * OptionController implements the CRUD actions for Option model.
 */
class OptionController extends CustomController
{

    public $customactions = [
        'get','getlist','gethtmllist','save'
    ];
    
    public function getCustomActions(){
        return $this->customactions;
    }
    
    public function setCustomActions($customactions = []) {
        return parent::setCustomActions($this->getCustomActions());
    }

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
     * Lists all Option models.
     * @return mixed
     * @throws Exception
     */
    public function actionIndex()
    {
        $model = new Option();
        $model->IdType = Type::findOne(['Code'=>Option::TYPE_MODULE])->Id;
        $model->IdUrlType = Type::findOne(['Code'=>Option::URL_INSIDE])->Id;
        $model->IdParent = NULL;
        $model->RequireAuth = Option::REQUIRE_AUTH_TRUE;
        
        $modelGroup = new Option();
        $modelGroup->IdType = Type::findOne(['Code'=>Option::TYPE_GROUP])->Id;
        $modelGroup->IdUrlType = $model->IdUrlType;
        $modelGroup->IdParent = NULL;
        $modelGroup->RequireAuth = Option::REQUIRE_AUTH_TRUE;
        
        $modelController = new Option();
        $modelController->IdType = Type::findOne(['Code'=>Option::TYPE_CONTROLLER])->Id;
        $modelController->IdUrlType = $model->IdUrlType;
        $modelController->IdParent = NULL;
        $modelController->RequireAuth = Option::REQUIRE_AUTH_TRUE;
        
        $modelAction = new Option();
        $modelAction->IdType = Type::findOne(['Code'=>Option::TYPE_ACTION])->Id;
        $modelAction->IdUrlType = $model->IdUrlType;
        $modelAction->IdParent = NULL;
        $modelAction->ItemMenu = 0;
        $modelAction->RequireAuth = Option::REQUIRE_AUTH_TRUE;
        $modelAction->SaveLog = Option::SAVE_LOG_DISABLED;
        $modelAction->SaveTransaction = Option::SAVE_TRANSACION_DISABLED;
        
        $modelPermission = new Option();
        $modelPermission->IdType = Type::findOne(['Code'=>Option::TYPE_PERMISSION])->Id;
        $modelPermission->IdUrlType = $model->IdUrlType;
        $modelPermission->IdParent = NULL;
        $modelPermission->ItemMenu = 0;
        $modelPermission->RequireAuth = Option::REQUIRE_AUTH_TRUE;

        /*
        $searchModel = new OptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        */
        $searchModel = (new OptionSearch())->getOptionList();
        $dataProvider = new ArrayDataProvider([
            'allModels' => $searchModel,
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'modelGroup' => $modelGroup,
            'modelController' => $modelController,
            'modelAction' => $modelAction,
            'modelPermission' => $modelPermission,
        ]);
    }

    /**
     * Deletes an existing Option model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete()
    {
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {

            $data = Yii::$app->getRequest()->post( StringHelper::basename( Option::class));
            $id = ArrayHelper::getValue( $data, 'Id');
            $model = $this->findModel($id);
            $title = $model->IdType ? $model->type->Name:'Opción';
            $name = $model->Name;
            $dttitle = 'Eliminado';
            if($model->delete()){
                $response = [
                    'success'=>true,
                    'message'=>$title.' '.$name.' '.$dttitle,
                    'title'=>$title.' '.$dttitle,
                ];
            } else {
                $message = $this->getErrors($model->errors);
                throw new Exception($message, 90003);
            }
        } catch (Exception $ex){
            $response = [
                'success'=>false,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
    }

    /**
     * Finds the Option model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Option the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Option::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
    public function actionSave(){
        $model = new Option();
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if (Yii::$app->request->isAjax) {
                $data = Yii::$app->request->post('Option');
                $environment = isset($data[StringHelper::basename(Optionenvironment::class)]) ? $data[StringHelper::basename(Optionenvironment::class)]:NULL;
                unset($data[StringHelper::basename(Optionenvironment::class)]);
                $dttitle = 'Agregado';
                if(!empty($data['Id'])){
                    $model = $this->findModel($data['Id']);
                    $dttitle = 'Actualizado';
                    if($model == NULL){
                        throw new Exception('No se encontró registro', 90001);
                    }
                } 
                $model->attributes = $data;
                if(!empty($environment) && gettype($environment) == 'array'){
                    $model->Optionenvironment = $environment;
                    $model->_emptyEnvironments = false;
                } else {
                    $model->Optionenvironment = [];
                    $model->_emptyEnvironments = true;
                }
                if($model->save()){
                    $model->refresh();
                    $title = $model->IdType ? $model->type->Name:'Opción';
                    $response = array_merge(['success'=>true,'title'=>$title.' '.$dttitle],$model->attributes);
                } else {
                    #$message = $this->setMessageErrors($model->errors);
                    $message = $this->getErrors($model->errors);
                    throw new Exception($message, 90002);
                }
            }
            
        } catch (Exception $ex) {
            $response = [
                'success'=>false,
                'code'=>$ex->getCode(),
                'message'=>$ex->getMessage(),
                'errors'=>$model->errors,
            ];
        }
        return $response;
        #echo json_encode($response);
    }
    
    public function actionGet(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->post('data');
                $criteria = Json::decode($data, true);
                $option = Option::findOne($criteria);
                $response = array_merge(['success'=>true], $option->getExtendedValues());
            }
        } catch (Exception $exc) {
            $response = [
                'success'=>false,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionGetlist(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            if(\Yii::$app->request->isAjax){
                $data = \Yii::$app->request->get('data');
                $criteria = Json::decode($data, true);
                $term = $criteria['term'];
                unset($criteria["term"]);
                $options = Option::find()->where($criteria)
                        ->andFilterWhere(['like','Name',$term])
                        ->select(['Id as id','Name as label'])
                        ->asArray()
                        ->all();
                $response = [
                    'success'=>true,
                    'list'=>$options,
                ];
            }
        } catch (Exception $exc) {
            $response = [
                'success'=>false,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
            ];
        }
        return $response;
    }
    
    public function actionGethtmllist(){
        $response = [];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $list = (new Option())->getOptionList();//Option::getHtmlList();
                $response = [
                    'success'=>true,
                    'list'=>$list,
                ];
        } catch (Exception $exc) {
            $response = [
                'success'=>false,
                'message'=>$exc->getMessage(),
                'code'=>$exc->getCode(),
            ];
        }
        return $response;
    }
}
