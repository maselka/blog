<?php

namespace app\modules\api\models;

use Yii;

class AccessToken extends BaseAccessToken
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = [ 'token', 'timeStamp', 'userId' ];
        $scenarios[self::SCENARIO_UPDATE] = [ 'token', 'timeStamp' ];

        return $scenarios;
    }

    public static function findByUserId($id)
    {
        return static::findOne(['userId' => $id]);
    }

    public static function findUserIdByAccessToken($accessToken)
    {
        $token = static::findOne(['token' => $accessToken]);
        if($token) {
             $token = $token->getAttribute('userId');
        }

        return $token;
    }

    /**
     * Gets query for [[user]].
     *
     * @return \yii\db\ActiveRecord
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId'])->one();
    }
}
