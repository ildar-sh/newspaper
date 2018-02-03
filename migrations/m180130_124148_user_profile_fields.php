<?php

use yii\db\Migration;
use app\models\User;

/**
 * Class m180130_124148_user_profile_fields
 */
class m180130_124148_user_profile_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(User::tableName(), 'receive_events_by_email', $this->boolean()->notNull()->defaultValue(true));
        $this->addColumn(User::tableName(), 'receive_events_by_alert', $this->boolean()->notNull()->defaultValue(true));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'receive_events_by_email');
        $this->dropColumn(User::tableName(), 'receive_events_by_alert');
    }
}
