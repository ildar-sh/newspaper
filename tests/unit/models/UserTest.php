<?php

namespace tests\models;

use app\models\User;
use app\tests\fixtures\UserFixture;


class UserTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    public $tester;

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::className(),
                // fixture data located in tests/_data/user.php
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
        ];
    }

    public function testCreateUser()
    {
        expect_that($user = new User());
        $user->username = 'test';
        $user->password = 'test';
        expect_that($user->beforeSave(true));
        expect_that(!empty($user->password_hash));

        expect_that($userWithEmptyPassword = new User());
        $userWithEmptyPassword->username = 'test';
        expect_that($userWithEmptyPassword->beforeSave(true));
        expect_that(empty($userWithEmptyPassword->password_hash));

        return $user;
    }

    /**
     * @param User $user
     * @depends testCreateUser
     */
    public function testActivateUser($user)
    {
        expect($user->username)->equals('test');
        expect_not($user->active);
        $user->active = true;
        expect_that($user->active);
    }

    /**
     * @param User $user
     * @depends testCreateUser
     */
    public function testValidateUser($user)
    {
        expect_that($user->validatePassword('test'));
        expect_not($user->validatePassword('123456'));        
    }

    public function testFindByUsername()
    {
        $users = $this->tester->grabFixture('user');

        expect_that($user1 = User::findByUsername($users['user1']['username']));
        expect_that($user1->id == $users['user1']['id']);

        // user2 not active, so can't be found
        expect_not($user2 = User::findByUsername($users['user2']['username']));
        expect_that($user2 = User::findOne(['username' => $users['user2']['username']]));
        expect_that($user2->id == $users['user2']['id']);
    }

    public function testFindActiveUserByIdentity()
    {
        $users = $this->tester->grabFixture('user');

        expect_that($user1 = User::findIdentity($users['user1']['id']));
        expect_that($user1->username == $users['user1']['username']);

        // user2 not active, so can't be found
        expect_not($user2 = User::findIdentity($users['user2']['id']));
        expect_that($user2 = User::findOne($users['user2']['id']));
        expect_that($user2->username == $users['user2']['username']);
    }
}
