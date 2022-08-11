<?php
namespace client\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use backend\models\ChangePasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\models\Appointments;
use backend\models\Useroption;
use backend\models\Setting;
use backend\models\Option;
use yii\helpers\StringHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        if(!\Yii::$app->user->isGuest){
            $user = \Yii::$app->user->getIdentity();
            $user->setExpirationDate();
            if($user->expired){
                return $this->redirect('site/changepassword');
            } else {
                $this->_loadMenu();
            }
        } else {
            $this->_loadDefaultMenu();
        }
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->rememberMe = TRUE;
            if($model->login()){
                $user = \Yii::$app->user->getIdentity();
                $user->setExpirationDate();
                if($user->expired){
                    return $this->redirect('changepassword');
                } else {
                    if($user->warningPass){
                        $url = \Yii::$app->urlManager->createUrl(['site/changepassword']);
                        Yii::$app->session->setFlash('warning', 'Su contraseña expira en '.$user->remainingDays." días. Se recomienda actualizarla. <u>".\yii\helpers\Html::a('Actualizar', $url,['class'=>''])."</u>");
                    }
                    return $this->goBack();
                }
            } else {
                return $this->render('login', [
                    'model' => $model,
                ]);
            }
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
    
    public function actionChangepassword(){
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

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Verifique su correo electrónico.');
                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Lo Sentimos, no fue posible restablecer la contraseña usand el correo electrónico provisto');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
        
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Nueva contraseña guardada');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    
    private function _loadMenu(){
        try {
            if(Yii::$app->user->isGuest){
                $this->_loadDefaultMenu();
            } else {
                $session = \Yii::$app->session;
                $user = Yii::$app->user->getIdentity();
                
                $items = $session->get('itemsMenu');
                if(empty($items)){
                    $useroptions = new Useroption();
                    $useroptions->IdUser = $user->Id;
                    $useroptions->IdOption = NULL;
                    $itemsMenu = $useroptions->loadMenu();
                    $session->set('itemsMenu', $itemsMenu);
                }
                $this->_setSubMenuSettings();
            }
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
                    ->where(['KeyWord' => StringHelper::basename(Option::class), 'Code' => 'SUBMENU'])->one();
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
}
