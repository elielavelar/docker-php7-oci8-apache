<?php

namespace backend\controllers;

use Yii;
use common\models\User;
use backend\models\UserSearch;
use backend\models\Useroption;
use backend\models\Setting;
use backend\models\Option;
use common\models\Userpreference;
use common\models\Attachment;
use common\models\search\AttachmentSearch;
#use yii\web\Controller;
use backend\controllers\CustomController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use Exception;
use yii\web\UploadedFile;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends CustomController
{
    public $customactions = [
        'profile','getrandompass','getfilteruser','reloadoptions', 'upload'
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new User();

        return $this->render('index', [
            'model' => $model,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    public function actionProfile()
    {
        if($this->validateUser()){
            $model = $this->findModel(\Yii::$app->user->getIdentity()->getId());
            $model->getSettings();
            $model->setExpirationDate();
            if ($model->load(Yii::$app->request->post())) {
                $post = Yii::$app->request->post(StringHelper::basename($model->class));
                $model->_password = !empty($post['_password']) ? $post['_password']:NULL;
                $model->_passwordconfirm = !empty($post['_passwordconfirm']) ? $post['_passwordconfirm']:NULL;
                $model->updateOperator = TRUE;
                
                if($model->save()){
                    return $this->redirect(['profile']);
                } else {
                    $message = \Yii::$app->customFunctions->getErrors($model->errors);
                    Yii::$app->getSession()->setFlash('warning',$message);
                }
            } else {
                return $this->render('profile', [
                    'model' => $model,
                ]);
            }
        } else {
            $this->goHome();
        }
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $model->setExpirationDate();
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->scenario = User::SCENARIO_CREATE;
        $model->_password = \Yii::$app->customFunctions->getRandomPass();
        $model->_passwordconfirm = $model->_password;

        if ($model->load(Yii::$app->request->post())) {
            $model->generateAuthKey();
            if(!$model->save()){
                $this->viewErrors($model->errors);
                return $this->render('create', [
                    'model' => $model,
                ]);
            } else {
                return $this->redirect(['view', 'id' => $model->Id]);
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $useroptions = new Useroption();
        $useroptions->list = Useroption::getHtmlList(['IdUser' => $model->Id ]);
        
        $modelDetail = new Useroption();
        $modelDetail->IdUser= $model->Id;

        $searchAttachmentModel = new AttachmentSearch();
        $searchAttachmentModel->KeyWord = StringHelper::basename(User::class);
        $searchAttachmentModel->AttributeName = 'Id';
        $searchAttachmentModel->AttributeValue = $model->Id;
        $attachmentDataProvider = $searchAttachmentModel->search(Yii::$app->request->queryParams);

        $attachmentModel = new Attachment();
        $attachmentModel->KeyWord = StringHelper::basename(User::class);
        $attachmentModel->AttributeName = 'Id';
        $attachmentModel->AttributeValue = $model->Id;
        
        if ($model->load(Yii::$app->request->post())) {
            $post = Yii::$app->request->post(StringHelper::basename(User::class ));
            $model->_password = !empty($post['_password']) ? $post['_password']:NULL;
            $model->_passwordconfirm = !empty($post['_passwordconfirm']) ? $post['_passwordconfirm']:NULL;
            if(isset($post[StringHelper::basename(Useroption::class)])){
                $model->usersetting = $post[StringHelper::basename(Useroption::class)];
            } else {
                $model->usersetting = [];
                $model->_emptyUserOptions = TRUE;
            }
            if($model->save()){
                return $this->redirect(['view', 'id' => $model->Id ]);
            } else {
                $message = \Yii::$app->customFunctions->getErrors($model->errors);
                Yii::$app->getSession()->setFlash('warning',$message);
            }
            
        } else {
            $model->setExpirationDate();
            return $this->render('update', [
                'model' => $model, 'searchModel'=> $useroptions, 'modelDetail'=> $modelDetail,
                'attachmentModel' => $attachmentModel, 'searchAttachmentModel' => $searchAttachmentModel, 'attachmentDataProvider' => $attachmentDataProvider,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
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
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionGetrandompass(){
        $response = [
            'success'=> FALSE,
        ];
        \Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $response = [
                'success'=> TRUE,
                'password'=> \Yii::$app->getSecurity()->generateRandomString(User::DEFAULT_PASS_LENGTH),
            ];
        } catch (Exception $ex) {
            $response = [
                'success'=>FALSE,
                'message'=>$ex->getMessage(),
                'code'=>$ex->getCode(),
            ];
        }
        return $response;
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
    
    public function actionGetfilteruser($q = NULL, $idservicecentre = NULL){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [
            'results'=> ['id'=> '','text'=> '']
        ];
        if(!empty($q)){
            $result['results']= User::getFilterUser(['q' => $q, 'IdServiceCentre' => $idservicecentre]);
        }
        return $result;
    }
    
    public function actionReloadoptions(){
        try {
            $response = [];
            \Yii::$app->response->format = Response::FORMAT_JSON;
            try {
                if (Yii::$app->request->isAjax) {
                    $this->_loadMenu();
                    $response = [
                        'success' => true,
                        'message' => 'Opciones cargadas correctamente'
                    ];
                }

            } catch (Exception $ex) {
                $response = [
                    'success'=>FALSE,
                    'code'=>$ex->getCode(),
                    'message'=>$ex->getMessage(),
                ];
            }
            return $response;
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _loadMenu(){
        try {
            $session = \Yii::$app->session;
            $user = Yii::$app->user->getIdentity();

            $items = $session->get('itemsMenu');
            $useroptions = new Useroption();
            $useroptions->IdUser = $user->Id;
            $useroptions->IdOption = NULL;
            $itemsMenu = $useroptions->loadMenu();
            $session->set('itemsMenu', $itemsMenu);
            $this->_setSubMenuSettings();
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    private function _loadDefaultMenu(){
        try {
            $options = new Option();
            $session = Yii::$app->session;
            $session->open();
            $itemsMenu = $options->loadDefaultMenu();
            $session->set('itemsMenu', $itemsMenu);
        } catch (Exception $ex) {
            throw $ex;
        }
    }
    
    public function _setSubMenuSettings(){
        try {
            $session = \Yii::$app->session;
            $settings = Setting::find()
                    ->where(['KeyWord' => 'Options', 'Code' => 'SUBMENU'])->one();
            $submenu = [];
            if(!empty($settings)){
                foreach ($settings->settingsdetails as $detail){
                    $submenu[$detail->Code] = $detail->Value;
                }
            }
            $session->set('subMenu', $submenu);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function actionUpload(){
        try {
            $model = new Attachment();
            if(Yii::$app->request->isAjax){
                $data = Yii::$app->request->post(StringHelper::basename(User::class));
                $overwrite = isset($data[StringHelper::basename(Attachment::class)]['overwrite']) ? $data[StringHelper::basename(Attachment::class)]['overwrite']: false;
                $model->load($data);
                if(isset($data['renameFile']) && !empty($data['newName'])){
                    $model->renameFile = TRUE;
                    $model->newName = $data['newName'];
                }
                $member = $this->findModel($model->AttributeValue);
                $model->overwriteFile = $overwrite;
                $model->fileattachment = UploadedFile::getInstance($member, 'photo');
                if($model->save()){
                    $model->refresh();
                    $member->IdAttachmentPicture = $model->Id;
                    $member->save();
                    Yii::$app->session->setFlash('success', 'Datos Cargados Exitosamente');
                    return true;
                } else {
                    $message = Yii::$app->customFunctions->getErrors($model->errors);
                    throw new Exception($message, 90099);
                }
            }

        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function actionUploadbatch(){
        try {
            $model = new User();
            $model->scenario = User::SCENARIO_UPLOAD;
            if (Yii::$app->request->isPost) {
                $model->uploadFile = UploadedFile::getInstance($model, 'uploadFile');
                if(!$model->upload()){
                    $message = $this->getErrors($model->errors);
                    throw new Exception($message, 90000);
                }
            }
            return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
        } catch (Exception $exc) {
            echo false;
            print_r($model->errors);
            echo $exc->getMessage();
        }
    }
}
