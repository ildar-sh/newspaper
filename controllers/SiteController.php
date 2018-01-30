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
use yii\data\Pagination;

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
                        'actions' => ['logout'],
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
        $query = Post::find()->active();
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);
        $models = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
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
        $userProfile = new UserProfileForm(Yii::$app->user->getIdentity());
        return $this->render('profile', ['model' => $userProfile]);
    }

}
