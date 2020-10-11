<?php

namespace app\modules\api\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property string $date
 */
class Post extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'text', 'date'], 'required'],
            [['user_id'], 'integer'],
            [['text'], 'string'],
            [['date'], 'safe'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['text'];

        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'date' => 'Date',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserName()
    {
        $user = $this->hasOne(User::className(), ['id' => 'user_id'])->one();
        if ($user) {
           $user_name = $user->getAttribute('name');
        } else {
            $user_name = NULL;
        }

        return $user_name;
    }

    public static function getAll($limit, $offset = 0)
    {
        return Post::find()->limit($limit)->offset($offset)->all();
    }
}
