<?php

use yii\db\Migration;

/**
 * Class m180107_055339_create_news
 */
class m180107_055339_create_news extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'username' => $this->string(256)->notNull()->unique(),
            'password_hash' => $this->string(256),
            'auth_key' => $this->string(256),
            'access_token' => $this->string(256),
            'email' => $this->string(254),
            'email_confirmed' => $this->boolean()->notNull()->defaultValue(false),
            'active' => $this->boolean()->notNull()->defaultValue(false),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'last_visit' => $this->dateTime(),
        ]);

        $this->batchInsert('user', ['id','username', 'password_hash', 'active'], [
            [100,'admin',\Yii::$app->security->generatePasswordHash('admin'),true],
        ]);

        $this->createTable('post', [
            'id' => $this->primaryKey(),
            'name' => $this->string(256),
            'description' => $this->string(256),
            'image' => $this->string(1024),
            'short_text' => $this->text(),
            'long_text' => $this->text(),
            'status' => $this->integer(),
            'created' => $this->dateTime()->notNull()->defaultExpression('now()'),
            'author_id' => $this->integer()->notNull(),
        ]);

        $this->createIndex(
            'idx-post-author_id',
            'post',
            'author_id'
        );

        $this->addForeignKey(
            'fk-post-author_id',
            'post',
            'author_id',
            'user',
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
            'fk-post-author_id',
            'post'
        );

        $this->dropIndex(
            'idx-post-author_id',
            'post'
        );

        $this->dropTable('post');
        $this->dropTable('user');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180107_055339_create_news cannot be reverted.\n";

        return false;
    }
    */
}
