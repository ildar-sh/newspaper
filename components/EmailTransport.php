<?php
/**
 * Created by PhpStorm.
 * User: shamgunov
 * Date: 02.02.2018
 * Time: 18:11
 */

namespace app\components;

use app\models\CanBeViewed;

class EmailTransport
{
    public $name = 'email';

    public function markAsViewed(CanBeViewed $model, $user_id)
    {
        $model->markAsViewed($user_id, $this->name);
    }

    public function getNew(CanBeViewed $model, $user_id,\DateTime $fromDate, $limit = null)
    {
        return $model->getNewFor($user_id, $this->name, $fromDate, $limit);
    }
}