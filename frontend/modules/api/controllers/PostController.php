<?php
/**
 * @link http://blog.localhost
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/maselka/blog/blob/master/LICENSE.md
 */

namespace frontend\modules\api\controllers;

use app\modules\api\models\AccessToken;
use app\modules\api\models\Post;
use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;


/**
 * PostController provides API functionality for create and get posts
 *
 * @author Marsel Gabdullin <gabdullinmr@gmail.com>
 * @since 0.1
 */
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
                    'all' => ['GET'],
                    'user' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Create new post in blog
     *
     * @param string $accessToken Token for user identification
     * @param string $text Publication text
     * @return array Status and data or error massage
     */
    public function actionCreate($accessToken = '', $text = '')
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($accessToken == '') {
            return ['status' => false, 'error_massage' => 'parameter access_token is missing or empty string'];
        }

        $accessToken = AccessToken::findOne(['token' => $accessToken]);
        if (!$accessToken) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $user = $accessToken->user;
        if(!$user) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $post = new Post();
        $post->setAttribute('text', $text);
        $post->setAttribute('userId', $user->id);
        $post->setAttribute('date', date('Y-m-d H:i:s'));
        if (!$post->validate()) {
            return ['status' => false, 'error_massage' => $post->getErrors()];
        }

        if (!($post->save())) {
            return ['status' => false, 'error_massage' => $post->getErrors()];
        }

        return ['status' => true, 'data' => 'post published'];
    }

    /**
     * Getting any blog posts
     *
     * @param string $accessToken Token for user identification.
     * @param null $limit Number of posts requested. Optional field.
     * @param int $offset Number of posts received. Optional field.
     * @return array Array of posts.
     */
    public function actionAll($accessToken = '', $limit = NULL, $offset = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($accessToken == '') {
            return ['status' => false, 'error_massage' => 'Parameter access_token is missing or empty string'];
        }

        $accessToken = AccessToken::findOne(['token' => $accessToken]);
        if (!$accessToken) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $user = $accessToken->getUser();
        if(!$user) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $postsArray = [];
        $postQuery = Post::find()->limit($limit)->offset($offset);
        foreach ($postQuery->each() as $post) {
            array_push($postsArray, $post->serializeToArray());
        }

        return $postsArray;
    }

    /**
     * Getting posts user
     *
     * @param string $accessToken Token for user identification.
     * @param null $limit Number of posts requested. Optional field.
     * @param int $offset Number of posts received. Optional field.
     * @return array Array of posts.
     */
    public function actionUser($accessToken = '', $limit = NULL, $offset = 0)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($accessToken == '') {
            return ['status' => false, 'error_massage' => 'Parameter access_token is missing or empty string'];
        }

        $accessToken = AccessToken::findOne(['token' => $accessToken]);
        if (!$accessToken) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $user = $accessToken->user;
        if(!$user) {
            return ['status' => false, 'error_massage' => 'Incorrect access token'];
        }

        $posts = $user->posts;
        if (!$posts) {
            return ['status' => false, 'error_massage' => 'User did not create post'];
        }

        $postsArray = [];
        $postQuery = $user->getPosts()->limit($limit)->offset($offset);
        foreach ($postQuery->each() as $post) {
            array_push($postsArray, $post->serializeToArray());
        }

        return $postsArray;
    }
}
