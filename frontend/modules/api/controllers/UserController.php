<?php

namespace frontend\modules\api\controllers;

use app\modules\api\models\AccessToken;
use app\modules\api\models\User;
use Yii;
use yii\base\ErrorException;
use yii\web\Controller;
use yii\web\Response;

class UserController extends Controller
{
    public function actionIndex()
    {
        echo 'test';
        return $this->render('index');
    }

    public function actionAuth()
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        $param = Yii::$app->request->post();
        var_dump($param);
        try {
            $user = User::find()
            ->where(['email' => $param['email']])
            ->one();
            $password = $param['password'];
        } catch (ErrorException $e) {
            return ['status' => false, 'data' => $e->getMessage()];
        }

        if (!$user) {
            return ['status' => false, 'data' => 'wrong  email or password'];
        }

        $hash = $user->getAttribute('password');
        if ($app->security->validatePassword($password, $hash)) {
            return ['status' => false, 'data' => 'wrong  email or password'];
        }

        $access_token = AccessToken::find()
            ->where(['user_id' => $user->getAttribute('id')])
            ->one();
        if (!$access_token) {
            $access_token = new AccessToken();
            $access_token->setAttribute('user_id', $user->getAttribute('id'));
        }

        $access_token->setAttribute('token', $app->security->generateRandomString());
        $access_token->setAttribute('time_stamp', date("Y-m-d H:i:s"));

        return ['status' => true, 'data' => $access_token->getAttribute('token')];
    }

    public function actionCreate()
    {

    }
}
