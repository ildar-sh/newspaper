<?php
/**
 * Created by PhpStorm.
 * User: il
 * Date: 04.02.2018
 * Time: 14:49
 */

namespace app\components;

use Yii;
use yii\rbac\PhpManager;

class CustomManager extends PhpManager
{
    const DEFAULT_ROLE = 'default_role';

    public $defaultRoles = [self::DEFAULT_ROLE];

    public function init()
    {
        parent::init();
        if (!Yii::$app->user->isGuest) {
            /**
             * @var $user \app\models\User
             */
            $user = Yii::$app->user->getIdentity();
            $role = $user->role;

            $userRole = $this->getRole($role);
            $defaultRole = $this->getRole(self::DEFAULT_ROLE);

            $this->addChild($defaultRole, $userRole);
        }
    }

    protected function saveToFile($data, $file)
    {
        // do nothing
    }
}