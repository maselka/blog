<?php

namespace frontend\modules\api\controllers;

use app\modules\api\models\AccessToken;
use app\modules\api\models\Post;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;

class PostController extends Controller
{
    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'create' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCreate()
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        $param = $app->request->post();
        $post = new Post();
        $post->scenario = Post::SCENARIO_CREATE;
        $post->attributes = $param;
        $user_id = AccessToken::findUserIdByAccessToken($param['access_token']);
        if (!isset($param['access_token']) || !$user_id) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $post->setAttribute('user_id', $user_id);
        $post->setAttribute('date', date('Y-m-d H:i:s'));
        if (!$post->validate()) {
            return ['status' => false, 'error_massage' => $post->getErrors()];
        }

        if (!($post->save())) {
            return ['status' => false, 'error_massage' => $post->getErrors()];
        }

        return ['status' => true, 'data' => 'post published'];
    }

}
