<?php

namespace app\modules\api\models;

use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $password
 * @property string $role
 */
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
    public static function findByEmail($email)
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
}
