<?php

namespace frontend\modules\api\controllers;

use app\modules\api\models\AccessToken;
use app\modules\api\models\Post;
use app\modules\api\models\User;
use Yii;
use yii\base\ErrorException;
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
                    'get-all' => ['GET'],
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

        try {
            $user_id = AccessToken::findUserIdByAccessToken($param['access_token']);
        } catch (ErrorException $e) {
            return ['status' => false, 'error_massage' => $e->getMessage()];
        }

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

    public function actionAll()
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        $param = $app->request->getQueryParams();
        $offset = $param['offset'] ?? 0;
        $limit = $param['limit'] ?? NULL;

        try {
            $access_token = AccessToken::findOne(['token' => $param['access_token']]);
        } catch (ErrorException $e) {
            return ['status' => false, 'error_massage' => $e->getMessage()];
        }

        $user = $access_token->user;
        if(!$user) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $posts = Post::getAll($limit, $offset);
        foreach ($posts as &$post) {
            $author = $post->user->name;
            $post = $post->toArray();
            $post['author'] = $author;
        }

        return $posts;
    }

    public function actionUser()
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        $param = $app->request->getQueryParams();
        $offset = $param['offset'] ?? 0;
        $limit = $param['limit'] ?? NULL;

        try {
            $access_token = AccessToken::findOne(['token' => $param['access_token']]);
        } catch (ErrorException $e) {
            return ['status' => false, 'error_massage' => $e->getMessage()];
        }

        $user = $access_token->user;
        if(!$user) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $posts = $user->posts;
        if (!$posts) {
            return ['status' => false, 'error_massage' => 'User did not create post'];
        }

        $posts = array_slice($posts, $offset, $limit);
        foreach ($posts as &$post) {
            $post = $post->toArray();
            $post['author'] = $user->name;
        }

        return $posts;
    }
}
