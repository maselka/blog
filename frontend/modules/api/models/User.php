<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $password
 * @property string $role
 */
class User extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'name', 'password', 'role'], 'required'],
            [['email', 'name', 'password', 'role'], 'string', 'max' => 255],
            [['email'], 'unique'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['name', 'email', 'password'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'name' => 'Name',
            'password' => 'Password',
            'rule' => 'Rule',
        ];
    }
}
