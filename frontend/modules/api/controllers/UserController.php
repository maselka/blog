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
    public $enableCsrfValidation = false;

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
        try {
            $user = User::findByEmail($param['email']);
            $password = $param['password'];
        } catch (ErrorException $e) {
            return ['status' => false, 'error_massage' => $e->getMessage()];
        }

        if (!$user && !($user->validatePassword($password))) {
            return ['status' => false, 'error_massage' => 'wrong  email or password'];
        }

        $access_token = AccessToken::findByUserId($user->getId());
        if (!$access_token) {
            $access_token = new AccessToken();
            $access_token->setAttribute('user_id', $user->getId());
        }

        $access_token->setAttribute('token', $app->security->generateRandomString());
        $access_token->setAttribute('time_stamp', date("Y-m-d H:i:s"));
        $access_token->save();

        return ['status' => true, 'access_token' => $access_token->getAttribute('token')];
    }

    public function actionCreate()
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        $user = new User();
        $user->scenario = User::SCENARIO_CREATE;
        $user->attributes = $app->request->post();
        if (!$user->validate()) {
            return ['status' => false, 'error_massage' => $user->getErrors()];
        }

        $user->setPassword();
        $user->save();

        $access_token = new AccessToken();
        $access_token->setAttribute('user_id', $user->getAttribute('id'));
        $access_token->setAttribute('token', $app->security->generateRandomString());
        $access_token->setAttribute('time_stamp', date('Y-m-d H:i:s'));
        $access_token->save();

        return ['status' => true, 'access_token' => $access_token->getAttribute('token')];
    }
}
