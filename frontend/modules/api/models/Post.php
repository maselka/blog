<?php

namespace app\modules\api\models;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property string $date
 */
class Post extends BasePost
{
    const SCENARIO_CREATE = 'create';

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['text'];

        return $scenarios;
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

    public function serializeToArray()
    {
        $authorUser = $this->user;
        $data = [];
        $data['text'] = $this->text;
        $data['date'] = $this->date;
        $data['author'] = !empty($authorUser) ? $authorUser->name : null;

        return $data;
    }
}
