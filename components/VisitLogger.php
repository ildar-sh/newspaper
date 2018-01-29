<?php
/**
 * Created by PhpStorm.
 * User: il
 * Date: 29.01.2018
 * Time: 2:57
 */

namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\base\Event;
use app\models\User;

class VisitLogger extends BaseObject
{
    public function init()
    {
        Event::on(\yii\web\Application::className(), \yii\web\Application::EVENT_AFTER_REQUEST, [$this, 'log']);
        parent::init();
    }

    public function log(Event $event)
    {
        /**
         * @var $app \yii\web\Application
         */
        $app = $event->sender;

        /**
         * @var $userModel User
         */
        $userModel = $app->user->getIdentity();
        if ($userModel) {
            $now = new \DateTime();
            $userModel->last_visit = $now->format(\DateTime::ISO8601);
            $userModel->save(false);
        }
    }
}