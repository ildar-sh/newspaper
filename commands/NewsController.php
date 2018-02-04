<?php
/**
 * Created by PhpStorm.
 * User: shamgunov
 * Date: 02.02.2018
 * Time: 20:53
 */

namespace app\commands;

use app\components\EmailTransport;
use Yii;
use yii\console\Controller;
use app\models\Post;
use app\models\User;
use yii\helpers\Url;

class NewsController extends Controller
{
    public $emailFrom;
    public $defaultAction = 'send';

    public $transport;

    public function actionSend()
    {
        echo "Start\n";
        echo "This command is not thredsafe, do not run it in parallel!\n";

        $this->transport = new EmailTransport();

        // TODO this way to get users is not thredsafe, fix before run command in parallel
        $users = User::find()->active()->andWhere(['is not', 'receive_events_by_email_from_datetime', null])->all();

        foreach ($users as $user) {
            $this->processUser($user);
        }

        echo "End\n";
        return 0;
    }

    protected function processUser(User $user)
    {
        $message = '';
        $receive_events_from = new \DateTime($user->receive_events_by_email_from_datetime);
        $newPosts = $this->transport->getNew(new Post(), $user->id, $receive_events_from, 10);
        foreach($newPosts as $newPost) {
            $message .=  $this->createStringAbout($newPost). "\n";
        }
        if (!empty($message)) {
            $isMessageSent = $this->sendMessage($user->email, 'News', $message);
            if ($isMessageSent) {
                foreach($newPosts as $newPost) {
                    $this->transport->markAsViewed($newPost, $user->id);
                }
            }
        }
    }

    protected function createStringAbout(Post $post)
    {
        // TODO
        return $post->name . ' ' . Url::to(['post/full', 'id' => $post->id], true);
    }

    protected function sendMessage($to, $subject, $text)
    {
        $isMessageSent = Yii::$app->mailer->compose()
            ->setTo($to)
            ->setFrom($this->emailFrom)
            ->setSubject($subject)
            ->setTextBody($text)
            ->send();

        if ($isMessageSent) {
            Yii::trace("The $subject is sent to $to");
        } else {
            Yii::error("Isn't possible to send the message $subject, to $to");
        }
        return $isMessageSent;
    }
} 