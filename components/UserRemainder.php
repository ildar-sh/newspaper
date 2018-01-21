<?php
/**
 * Created by PhpStorm.
 * User: il
 * Date: 21.01.2018
 * Time: 12:10
 */

namespace app\components;

use Yii;
use yii\base\BaseObject;
use yii\base\Event;
use app\models\User;
use yii\mail\MailerInterface;

class UserRemainder extends BaseObject
{
    public $emailFrom;

    public function init()
    {
        Event::on(User::className(), User::EVENT_IS_REGISTERED, [$this, 'sendConfirmationCode']);
        parent::init();
    }


    public function sendConfirmationCode(Event $event)
    {
        /**
         * @var $user \app\models\User
         */
        $user = $event->sender;

        $text = $user->getConfirmationCode();

        Yii::$app->mailer->compose()
                ->setTo($user->email)
                ->setFrom($this->emailFrom)
                ->setSubject('Activation url')
                ->setTextBody($text)
                ->send();
    }
}