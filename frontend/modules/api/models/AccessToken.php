<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "access_token".
 *
 * @property int $id
 * @property int $user_id
 * @property string $token
 * @property string $time_stamp
 */
class AccessToken extends \yii\db\ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_token';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'token', 'time_stamp'], 'required'],
            [['user_id'], 'integer'],
            [['time_stamp'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['user_id'], 'unique'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = [ 'token', 'time_stamp', 'user_id' ];
        $scenarios[self::SCENARIO_UPDATE] = [ 'token', 'time_stamp' ];

        return $scenarios;
    }

    public static function findByUserId($id)
    {
        return static::findOne(['user_id' => $id]);
    }

    public static function findUserIdByAccessToken($access_token)
    {
        $token = static::findOne(['token' => $access_token]);
        if($token) {
             $token = $token->getAttribute('user_id');
        }

        return $token;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
