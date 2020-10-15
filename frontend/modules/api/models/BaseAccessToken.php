<?php

namespace app\modules\api\models;

use Yii;

/**
 * This is the model class for table "accessToken".
 *
 * @property int $id
 * @property int $userId
 * @property string $token
 * @property string $timeStamp
 */
class BaseAccessToken extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accessToken';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'token', 'timeStamp'], 'required'],
            [['userId'], 'integer'],
            [['timeStamp'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['userId'], 'unique'],
            [['userId'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['userId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'token' => 'Token',
            'timeStamp' => 'Time Stamp',
        ];
    }
}
