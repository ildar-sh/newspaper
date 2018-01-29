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
use yii\helpers\Url;

class UserRemainder extends BaseObject
{
    public $emailFrom;

    public function init()
    {
        Event::on(User::className(), User::EVENT_IS_REGISTERED, [$this, 'sendConfirmationCode']);
        Event::on(User::className(), User::EVENT_IS_CREATED_BY_ADMIN, [$this, 'sendConfirmationAndResetPasswordCode']);
        parent::init();
    }

    public function sendConfirmationCode(Event $event)
    {
        /**
         * @var $user \app\models\User
         */
        $user = $event->sender;

        $confirmationCode = $user->getConfirmationCode();
        // todo add description text
        $text = Url::toRoute(['/site/confirm-email','ConfirmEmailForm[confirmation_code]' => $confirmationCode], true);

        $this->sendMessage($user->email, 'Activation url', $text);
    }

    public function sendConfirmationAndResetPasswordCode(Event $event)
    {
        /**
         * @var $user \app\models\User
         */
        $user = $event->sender;

        $confirmationCode = $user->getConfirmationCode();
        // todo add description text
        $text = Url::toRoute(['/site/confirm-email-and-set-password','ConfirmEmailForm[confirmation_code]' => $confirmationCode], true);

        $this->sendMessage($user->email, 'Activation url for ' . $user->username, $text);
    }

    protected function sendMessage($to, $subject, $text)
    {
        $isMessageSent = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom($this->emailFrom)
            ->setSubject('Activation url')
            ->setTextBody($text)
            ->send();

        if ($isMessageSent) {
            Yii::$app->session->setFlash('info', "The $subject is sent");
            Yii::$app->session->setFlash('info', $text); // todo убрать
        } else {
            Yii::$app->session->setFlash('error', 'It isn\'t possible to send the message, check the email');
        }
    }
}