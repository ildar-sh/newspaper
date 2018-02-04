<?php
/**
 * Created by PhpStorm.
 * User: il
 * Date: 04.02.2018
 * Time: 12:37
 */

namespace app\commands;

use app\models\User;
use Yii;
use yii\console\Controller;
use app\rbac\AuthorRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        $auth->removeAll();

        $managePosts = $auth->createPermission('managePost');
        $managePosts->description = 'Manage posts';
        $auth->add($managePosts);

        $viewPost = $auth->createPermission('viewPost');
        $viewPost->description = 'View post';
        $auth->add($viewPost);

        $createPost = $auth->createPermission('createPost');
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        $deletePost = $auth->createPermission('deletePost');
        $deletePost->description = 'Delete post';
        $auth->add($deletePost);

        // add the rule
        $rule = new AuthorRule();
        $auth->add($rule);

        $updateOwnPost = $auth->createPermission('updateOwnPost');
        $updateOwnPost->description = 'Update own post';
        $updateOwnPost->ruleName = $rule->name;
        $auth->add($updateOwnPost);

        $auth->addChild($updateOwnPost, $updatePost);

        $deleteOwnPost = $auth->createPermission('deleteOwnPost');
        $deleteOwnPost->description = 'Delete own post';
        $deleteOwnPost->ruleName = $rule->name;
        $auth->add($deleteOwnPost);

        $auth->addChild($deleteOwnPost, $deletePost);

        $user = $auth->createRole(User::ROLE_USER);
        $auth->add($user);
        $auth->addChild($user, $viewPost);

        $manager = $auth->createRole(User::ROLE_MANAGER);
        $auth->add($manager);
        $auth->addChild($manager, $managePosts);
        $auth->addChild($manager, $createPost);
        $auth->addChild($manager, $user);
        $auth->addChild($manager, $updateOwnPost);
        $auth->addChild($manager, $deleteOwnPost);

        // добавляем роль "admin" и даём роли разрешение "updatePost"
        // а также все разрешения роли "author"
        $admin = $auth->createRole(User::ROLE_ADMIN);
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $deletePost);
        $auth->addChild($admin, $manager);

        $defaultRole = $auth->createRole(\app\components\CustomManager::DEFAULT_ROLE);
        $auth->add($defaultRole);
    }
}