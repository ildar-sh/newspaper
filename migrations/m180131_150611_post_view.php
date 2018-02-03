<?php

use yii\db\Migration;

/**
 * Class m180131_150611_post_view
 */
class m180131_150611_post_view extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('post_view',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'post_id' => $this->integer(),
            'transport' => $this->string('127'),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
        ]);

        $this->createIndex(
            'idx-post_view-user_id',
            'post_view',
            'user_id'
        );

        $this->addForeignKey(
            'fk-post_view-user_id',
            'post_view',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-post_view-post_id',
            'post_view',
            'post_id'
        );

        $this->addForeignKey(
            'fk-post_view-post_id',
            'post_view',
            'post_id',
            'post',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-post_view-post_id',
            'post_view'
        );

        $this->dropIndex(
            'idx-post_view-post_id',
            'post_view'
        );

        $this->dropForeignKey(
            'fk-post_view-user_id',
            'post_view'
        );

        $this->dropIndex(
            'idx-post_view-user_id',
            'post_view'
        );

        $this->dropTable('post_view');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180131_150611_user_viewed_events cannot be reverted.\n";

        return false;
    }
    */
}
