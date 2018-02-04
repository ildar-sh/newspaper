<?php

namespace app\controllers;

use app\models\ConfirmEmailForm;
use app\models\UserProfileForm;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\RegisterForm;
use app\models\Post;
use app\models\User;
use yii\data\Pagination;
use yii\web\ForbiddenHttpException;
use app\components\FlashTransport;

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
                'only' => ['register', 'login', 'logout', 'profile'],
                'rules' => [
                    [
                        'actions' => ['register', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
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
     * @return string
     */
    public function actionIndex()
    {
        $query = Post::find()->active()->orderBy(['created' => SORT_DESC]);
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        if (!Yii::$app->user->isGuest) {
            /**
             * @var $user User
             */
            $user = Yii::$app->user->getIdentity();
            $flashTransport = new FlashTransport();
            $receive_events_from = new \DateTime($user->receive_events_by_alert_from_datetime);
            $newPosts = $flashTransport->getNew(new Post(), $user->id, $receive_events_from, 10);
        } else {
            $newPosts = array();
        }

        return $this->render('index', [
            'newPosts' => $newPosts,
            'models' => $models,
            'pages' => $pages,
        ]);
    }

    /**
     * Register user action
     */
    public function actionRegister()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->register()) {
            Yii::$app->user->login($model->getUser());
            return $this->goBack();
        }
        return $this->render('register', [
            'model' => $model,
        ]);
    }

    public function actionConfirmEmail()
    {
        $model = new ConfirmEmailForm();
        $model->scenario = ConfirmEmailForm::SCENARIO_CONFIRM;

        if (!Yii::$app->request->isPost) {
            $dataLoaded = $model->load(Yii::$app->request->get());
        } else {
            $dataLoaded = $model->load(Yii::$app->request->post());
        }

        if ($dataLoaded && $model->confirmEmailAndLogin()) {
            Yii::$app->session->addFlash('Email confirmed');
            return $this->goHome();
        }

        return $this->render('confirmEmail', [
            'model' => $model,
        ]);
    }

    public function actionConfirmEmailAndSetPassword()
    {
        $model = new ConfirmEmailForm();
        $model->scenario = ConfirmEmailForm::SCENARIO_CONFIRM_AND_SET_PASSWORD;

        $model->load(Yii::$app->request->get());

        if ($model->load(Yii::$app->request->post()) && $model->confirmEmailAndLogin()) {
            Yii::$app->session->addFlash('Email confirmed');
            return $this->goHome();
        }

        return $this->render('confirmEmail', [
            'model' => $model,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionProfile()
    {
        if (!Yii::$app->user->isGuest) {
            $userProfile = new UserProfileForm(Yii::$app->user->getIdentity());
            if ($userProfile->load(Yii::$app->request->post())) {
                if ($userProfile->save()) {
                    Yii::$app->session->addFlash('success', 'Profile settings successfully saved');
                } else {
                    Yii::$app->session->addFlash('error', 'Fail to save profile. Change input data or try later.');
                }
            }
            return $this->render('profile', ['model' => $userProfile]);
        } else {
            throw new ForbiddenHttpException(Yii::t('app', 'Login to see the profile settings.'));
        }

    }

}
