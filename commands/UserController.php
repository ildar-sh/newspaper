<?php
/**
 * Created by PhpStorm.
 * User: shamgunov
 * Date: 02.02.2018
 * Time: 20:53
 */

namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\User;

class UserController extends Controller
{
    public $defaultAction = 'create-admin';

    public function actionCreateAdmin($username, $password, $email)
    {
        echo "Start\n";

        $admin = new User();
        $admin->username = $username;
        $admin->password = $password;
        $admin->email = $email;
        $admin->role = User::ROLE_ADMIN;
        $admin->active = true;

        if ($admin->save()) {
            echo "Admin created\n";
            return 0;
        } else {
            echo print_r($admin->getFirstErrors());
            return 1;
        }
    }
} 