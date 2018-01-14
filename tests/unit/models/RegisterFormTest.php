<?php

namespace tests\models;

use app\models\RegisterForm;
use app\tests\fixtures\UserFixture;

class RegisterFormTest extends \Codeception\Test\Unit
{
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

    private $model;
    /**
     * @var \UnitTester
     */
    public $tester;

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testRegisterExistingUser()
    {
        $this->model = new RegisterForm([
            'username' => 'user1',
            'password' => 'password',
            'repeatPassword' => 'password',
            'email' => 'test.test@gmail.com',
        ]);

        expect_not($this->model->register());
        expect_that(\Yii::$app->user->isGuest);
        expect($this->model->errors)->hasKey('username');
    }

    public function testRegisterEmptyPassword()
    {
        $this->model = new RegisterForm([
            'username' => 'newUser',
            'password' => '',
        ]);

        expect_not($this->model->register());
        expect($this->model->errors)->hasKey('password');
    }

    public function testRegisterEmptyEmail()
    {
        $this->model = new RegisterForm([
            'username' => 'newUser',
            'password' => 'password',
            'repeatPassword' => 'password',
            'email' => '',
        ]);

        expect_not($this->model->register());
        expect($this->model->errors)->hasKey('email');
    }

    public function testRegisterIncorrectEmail()
    {
        $this->model = new RegisterForm([
            'username' => 'newUser',
            'password' => 'password',
            'repeatPassword' => 'password',
            'email' => 'email',
        ]);

        expect_not($this->model->register());
        expect($this->model->errors)->hasKey('email');
    }

    public function testRegisterIncorrectRepeatPassword()
    {
        $this->model = new RegisterForm([
            'username' => 'user3',
            'password' => 'user3',
            'repeatPassword' => 'user33',
            'email' => 'user3@gmail.com',
        ]);

        expect_not($this->model->register());
        expect($this->model->errors)->hasKey('repeatPassword');
    }

    public function testRegisterCorrect()
    {
        $this->model = new RegisterForm([
            'username' => 'user3',
            'password' => 'user3',
            'repeatPassword' => 'user3',
            'email' => 'user3@gmail.com',
        ]);

        expect_that($this->model->register());
//        $emailMessage = $this->tester->grabLastSentEmail();
//        expect('valid email is sent', $emailMessage)->isInstanceOf('yii\mail\MessageInterface');
//        expect($emailMessage->getTo())->hasKey('user3@gmail.com');
//        expect($emailMessage->getFrom())->hasKey('no-replay@newspaper.com');
//        expect($emailMessage->getSubject())->equals('Activation url');
    }

}
