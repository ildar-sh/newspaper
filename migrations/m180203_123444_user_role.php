<?php

use yii\db\Migration;
use app\models\User;

/**
 * Class m180203_123444_user_role
 */
class m180203_123444_user_role extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn(User::tableName(),'role',$this->string(15)->notNull()->defaultValue(User::DEFAULT_ROLE));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn(User::tableName(), 'role');
    }
}
