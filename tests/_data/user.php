<?php
/**
 * Created by PhpStorm.
 * User: il
 * Date: 14.01.2018
 * Time: 17:40
 */

return [
    'user1' => [
        'username' => 'user1',
        'password_hash' => \Yii::$app->security->generatePasswordHash('user1'),
        'email' => 'user1@gmail.com',
        'active' => true,
    ],
    'user2' => [
        'username' => 'user2',
        'password_hash' => \Yii::$app->security->generatePasswordHash('user2'),
        'email' => 'user2@gmail.com',
        'active' => false,
    ],
];