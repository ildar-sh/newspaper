<?php

use yii\db\Migration;

/**
 * Class m180129_005311_update_news
 */
class m180129_005311_update_news extends Migration
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
}
