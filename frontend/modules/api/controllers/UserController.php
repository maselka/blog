<?php
/**
 * @link http://blog.localhost
 * @copyright Copyright (c) 2016 Lookahead Consulting
 * @license https://github.com/maselka/blog/blob/master/LICENSE.md
 */

namespace frontend\modules\api\controllers;

use app\modules\api\models\AccessToken;
use app\modules\api\models\User;
use Yii;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\Response;

/**
 * UserController provides API functionality for authorization and create user
 *
 * @author Marsel Gabdullin <gabdullinmr@gmail.com>
 * @since 0.1
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * @param string $email Email address
     * @param string $password Password
     * @return array Status and access token or error massage
     */
    public function actionAuth($email = '', $password = '')
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        switch ('') {
            case $email :
                return ['status' => false, 'error_massage' => 'Parameter email is missing or empty string'];
            case $password :
                return ['status' => false, 'error_massage' => 'Parameter password is missing or empty string'];
        }

        $user = User::findByEmail($email);
        if (!$user || !($user->validatePassword($password))) {
            return ['status' => false, 'error_massage' => 'wrong  email or password'];
        }

        $access_token = $user->getAccessToken();
        if (!$access_token) {
            $access_token = new AccessToken();
            $access_token->scenario = AccessToken::SCENARIO_CREATE;
        } else {
            $access_token->scenario = AccessToken::SCENARIO_UPDATE;
        }

        try {
            $access_token->attributes = [
                'token' => $app->security->generateRandomString(),
                'time_stamp' => date("Y-m-d H:i:s"),
                'user_id' => $user->id,
            ];
            $access_token->save();
        } catch (Exception $e) {
            return ['status' => false, 'error_massage' => 'Failed to create token'];
        }

        return ['status' => true, 'access_token' => $access_token->getAttribute('token')];
    }

    /**
     * @param string $email Email address
     * @param string $password Password
     * @param string $name User name
     * @return array Status and access token or error massage
     */
    public function actionCreate($email = '', $password = '', $name = '')
    {
        $app = Yii::$app;
        $app->response->format = Response::FORMAT_JSON;
        switch ('') {
            case $email :
                return ['status' => false, 'error_massage' => 'Parameter email is missing or empty string'];
            case $password :
                return ['status' => false, 'error_massage' => 'Parameter password is missing or empty string'];
            case $name :
                return ['status' => false, 'error_massage' => 'Parameter name is missing or empty string'];
        }

        $user = new User();
        $user->scenario = User::SCENARIO_CREATE;
        $user->attributes = [
            'email' => $email,
            'password' => $password,
            'name' => $name,
        ];
        if (!$user->validate()) {
            return ['status' => false, 'error_massage' => $user->getErrors()];
        }

        try {
            $user->setPassword();
            $user->save();
            $access_token = new AccessToken();
            $access_token->scenario = AccessToken::SCENARIO_CREATE;
            $access_token->attributes = [
                'token' => $app->security->generateRandomString(),
                'time_stamp' => date("Y-m-d H:i:s"),
                'user_id' => $user->id,
            ];
            $access_token->save();
        } catch (Exception $e) {
            return ['status' => false, 'error_massage' => 'Failed to create token'];
        }

        return ['status' => true, 'access_token' => $access_token->getAttribute('token')];
    }
}
