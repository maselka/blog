<?php


namespace app\modules\api\models;


use yii\base\Model;

class AuthByLoginForm extends Model
{
    public $email;
    public $password;

    /**
     * @var User
     */
    private $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password'], 'required'],
            [['password'], 'string'],
            [['email'], 'validateEmail'],
            [['password'], 'validatePassword'],
        ];
    }

    public function validateEmail($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        $this->_user = User::findByEmail($this->email);
        if (!$this->_user) {
            $this->addError($attribute, 'wrong email or password.');

            return false;
        }

        return true;
    }

    public function validatePassword($attribute)
    {
        if ($this->hasErrors()) {
            return false;
        }

        if (!$this->_user->validatePassword($this->password)) {
            $this->addError($attribute, 'wrong email or password.');

            return false;
        }

        return true;
    }

    public function login()
    {
        if ($this->hasErrors()) {
            return false;
        }

        return $this->_user;
    }
}