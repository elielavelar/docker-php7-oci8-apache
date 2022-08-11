<?php
namespace frontend\controllers;

use common\models\ChangePasswordForm;
use common\models\CompanyForm;
use common\models\User;
use Yii;
use yii\authclient\AuthAction;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use limefamily\OpenIdConnect\Keycloak;

/**
 * Site controller
 */
class SiteController extends Controller
{
    private $loginLayout = 'main-login';
    private $emptyLayout = 'empty';
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => [
                    //'auth',
                    'error'],
                'rules' => [
                    [
                        'actions' => ['login', 'error', 'selectcompany'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'changepassword'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class' => AuthAction::class,
                'successCallback' => [ $this, 'onAuthSuccess']
            ],
        ];
    }

    /**
     * @param Keycloak $client
     * @throws ForbiddenHttpException
     */
    public function onAuthSuccess($client){
        $attributesMap = [
            'preferred_username' => 'Username',
            'email' => 'Email'
        ];
        $attributes = $client->getUserAttributes();
        var_dump($attributes); die();
        $userAttributes = [];

    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(!Yii::$app->getUser()->isGuest){
            $user = \Yii::$app->getUser()->getIdentity();
            $user->setExpirationDate();
            if($user->expired){
                return $this->redirect(['site/changepassword']);
            } else {
                $this->_loadMenu();
            }
        }
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $this->layout = $this->loginLayout;
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }


        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $model->password = '';
            return $this->render('login', [
                'model' => $model,
            ]);
        }

        return $this->render('login', [ 'model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionSelectcompany()
    {
        $this->layout = $this->loginLayout;
        if (Yii::$app->user->isGuest) {
            return $this->redirect('login');
        }

        $model = new CompanyForm();
        if ($model->load(Yii::$app->request->post())) {
            return $model->setCompany() ? $this->goHome() : $this->redirect('login');
        }
        return $this->render('selectcompany', ['model' => $model]);
    }

    /**
     * @var $user User
     * @throws \Throwable
     */
    private function _loadMenu(){
        try {
            $user = Yii::$app->getUser()->getIdentity();
            $user->getUserMenu();
        } catch (\Throwable $th){
            throw $th;
        }
    }

    private function _loadDefaultMenu(){
        try {
            $itemsMenu = Yii::$app->customFunctions->loadDefaultMenu();
            $session = Yii::$app->session;
            $session->set('itemsMenu', $itemsMenu);
        } catch (\Throwable $th){
            throw $th;
        }
    }

    public function actionChangepassword(){
        $this->layout = $this->emptyLayout;
        $user = \Yii::$app->user->getIdentity();
        $user->setExpirationDate();
        $expired = $user->expired;

        $model = new ChangePasswordForm();
        if ($model->load(Yii::$app->request->post())) {
            if($user->validatePassword($model->oldPassword)){
                if($model->setPassword()){
                    Yii::$app->session->setFlash('success', 'Su Contraseña ha sido actualizada');
                    if($expired){
                        return $this->redirect(['site/logout']);
                    } else {
                        return $this->goBack();
                    }
                } else {

                }
            } else {
                $model->addError('oldPassword','Contraseña anterior no válida');
            }
        } else {
            if($user->expired){
                Yii::$app->session->setFlash('error', 'Su contraseña ha expirado');
            }
        }

        return $this->render('changePassword', [
            'model' => $model,
        ]);
    }
}
