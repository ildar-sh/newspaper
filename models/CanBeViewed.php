<?php
/**
 * Created by PhpStorm.
 * User: shamgunov
 * Date: 02.02.2018
 * Time: 18:20
 */

namespace app\models;


interface CanBeViewed
{
    public function markAsViewed($user_id, $transport);
    public function getNewFor($userId, $transport,\DateTime $dateFrom, $limit);
} 