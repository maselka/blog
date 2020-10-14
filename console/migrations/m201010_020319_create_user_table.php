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
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull()->unique(),
            'name' => $this->string()->notNull(),
            'password' => $this->string()->notNull(),
            'role' => $this->string(255)->notNull()->defaultValue('user'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
