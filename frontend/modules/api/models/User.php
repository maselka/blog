<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Exception;

class User extends BaseUser
{
    const SCENARIO_CREATE = 'create';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'email', 'password'];

        return $scenarios;
    }

    public function setPassword($password = null)
    {
        if (!$password) {
            $password = $this->password;
        }

        try {
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($password);
        } catch (Exception $e) {

            return false;
        }

        return true;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Finds user by Email
     *
     * @param string $email
     * @return User|null
     */
    public static function findByEmail(string $email)
    {
        return static::findOne(['email' => $email]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public static function getNameById()
    {
        return User::find()->select('name')->all();
    }

    public function getAccesstoken()
    {
        return $this->hasMany(Post::className(), ['userId' => 'id'])->one();
    }
}
