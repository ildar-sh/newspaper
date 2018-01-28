<?php

use yii\db\Migration;

/**
 * Class m290107_005311_update_news
 */
class m290107_005311_update_news extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn('post', 'status');
        $this->addColumn('post', 'active', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->addColumn('post', 'status', $this->integer());
        $this->dropColumn('post', 'active');
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
