<?php

namespace app\controllers;

use app\components\FlashTransport;
use Yii;
use app\models\Post;
use app\models\PostSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * PostController implements the CRUD actions for Post model.
 */
class PostController extends Controller
{
    /**
     * @inheritdoc
     */
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

    /**
     * Lists all Post models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Post model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Post model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Post();

        if ($model->load(Yii::$app->request->post())) {
            $model->image_file = UploadedFile::getInstance($model, 'image_file');
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->isAjax) {
            $renderMethod = 'renderAjax';
        } else {
            $renderMethod = 'render';
        }
        return $this->$renderMethod('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Post model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->image_file = UploadedFile::getInstance($model, 'image_file');
            if ($model->save()) {
                return $this->redirect(['index']);
            }
        }

        if (Yii::$app->request->isAjax) {
            $renderMethod = 'renderAjax';
        } else {
            $renderMethod = 'render';
        }
        return $this->$renderMethod('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Post model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (!$model->delete()) {
            Yii::$app->session->setFlash('error', implode('<br/>', $model->getFirstErrors()));
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Post model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }

    public function actionActivate($id, $status)
    {
        $model = $this->findModel($id);
        $model->active = $status == 'true';
        if ($model->save()) {
            return 'Ok';
        } else {
            $response = new Response();
            $response->setStatusCode(500);
            $response->data = print_r($model->getFirstErrors(), true);
            return $response;
        }
    }

    public function actionFull($id)
    {
        $model = $this->findModel($id);
        return $this->render('full', ['model' => $model]);
    }

    public function actionMarkAsRead($id)
    {
        $post = $this->findModel($id);
        if (!Yii::$app->user->isGuest) {
            $transport = new FlashTransport();
            $transport->markAsViewed($post, Yii::$app->user->getId());
        }
    }
}
