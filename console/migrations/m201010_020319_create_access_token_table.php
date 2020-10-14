<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%post}}`.
 */
class m201010_020319_create_post_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%accessToken}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull()->unique(),
            'token' => $this->string()->notNull(),
            'timeStamp' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex(
            'idx-accessToken-userId',
            'accessToken',
            'userId'
        );

        $this->addForeignKey(
            'fk-accessToken-userId',
            'accessToken',
            'userId',
            'user',
            'id',
            'NO ACTION',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%accessToken}}');

        $this->dropForeignKey(
            'fk-accessToken-userId',
            'accessToken',
        );

        $this->dropIndex(
            'idx-accessToken-userId',
            'accessToken',
        );
    }
}
