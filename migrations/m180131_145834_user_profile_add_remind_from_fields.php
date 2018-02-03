<?php

use yii\db\Migration;
use app\models\User;

/**
 * Class m180131_145834_user_profile_add_remind_from_fields
 */
class m180131_145834_user_profile_add_remind_from_fields extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->dropColumn(User::tableName(), 'receive_events_by_email');
        $this->dropColumn(User::tableName(), 'receive_events_by_alert');
        $this->addColumn(User::tableName(), 'receive_events_by_email_from_datetime', $this->dateTime()->defaultExpression('now()'));
        $this->addColumn(User::tableName(), 'receive_events_by_alert_from_datetime', $this->dateTime()->defaultExpression('now()'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'receive_events_by_email_from_datetime');
        $this->dropColumn(User::tableName(), 'receive_events_by_alert_from_datetime');
        $this->addColumn(User::tableName(), 'receive_events_by_email', $this->boolean()->notNull()->defaultValue(true));
        $this->addColumn(User::tableName(), 'receive_events_by_alert', $this->boolean()->notNull()->defaultValue(true));

    }
}
