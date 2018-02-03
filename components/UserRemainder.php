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
use yii\web\Response;

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

//        Yii::$app->response->on(
//            Response::EVENT_AFTER_SEND,
//            [$this, 'sendMessage'],
//            ['email' => $user->email, 'subject' => 'Activation url', 'message' => $text]
//        );
        $event = new Event();
        $event->data = ['email' => $user->email, 'subject' => 'Activation url', 'message' => $text];
        $this->sendMessage($event);
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

//        Yii::$app->response->on(
//            Response::EVENT_AFTER_SEND,
//            [$this, 'sendMessage'],
//            ['email' => $user->email, 'subject' => 'Activation url for ' . $user->username, 'message' => $text]
//        );
        $event = new Event();
        $event->data = ['email' => $user->email, 'subject' => 'Activation url for ' . $user->username, 'message' => $text];
        $this->sendMessage($event);
    }

    public function sendMessage(Event $event)
    {
        $to = $event->data['email'];
        $subject = $event->data['subject'];
        $text = $event->data['message'];

        $this->releaseUserConnection();

        $isMessageSent = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom($this->emailFrom)
            ->setSubject($subject)
            ->setTextBody($text)
            ->send();

        if ($isMessageSent) {
            Yii::info("The $subject is sent to $to");
        } else {
            Yii::error("Isn't possible to send the message $subject, to $to");
        }
    }

    protected function releaseUserConnection()
    {
//        Yii::$app->session->close();
//        YII_DEBUG ? fastcgi_finish_request() : @fastcgi_finish_request();
    }
}