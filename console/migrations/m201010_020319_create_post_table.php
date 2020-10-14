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
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'text' => $this->text()->notNull(),
            'date' => $this->dateTime()->notNull(),
        ]);

        $this->createIndex(
            'idx-post-userId',
            'post',
            'userId'
        );

        $this->addForeignKey(
            'fk-post-userId',
            'post',
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
        $this->dropTable('{{%post}}');

        $this->dropForeignKey(
            'fk-post-userId',
            'post',
        );

        $this->dropIndex(
            'idx-post-userId',
            'post',
        );
    }
}
