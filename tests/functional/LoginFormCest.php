<?php

class LoginFormCest
{
    public function _fixtures()
    {
        return [
            'user' => [
                'class' => \app\tests\fixtures\UserFixture::className(),
                // fixture data located in tests/_data/user.php
                'dataFile' => codecept_data_dir() . 'user.php'
            ],
        ];
    }

    public function _before(\FunctionalTester $I)
    {
        $I->amOnRoute('site/login');
    }

    public function openLoginPage(\FunctionalTester $I)
    {
        $I->see('Login', 'h1');

    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginById(\FunctionalTester $I)
    {
        $user = $I->grabFixture('user', 'user1');
        $I->amLoggedInAs($user['id']);
        $I->amOnPage('/');
        $I->see('Logout (' . $user['username'] . ')');
    }

    // demonstrates `amLoggedInAs` method
    public function internalLoginByInstance(\FunctionalTester $I)
    {
        $user = $I->grabFixture('user', 'user1');
        $I->amLoggedInAs(\app\models\User::findByUsername($user['username']));
        $I->amOnPage('/');
        $I->see('Logout (' . $user['username'] . ')');
    }

    public function loginWithEmptyCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', []);
        $I->expectTo('see validations errors');
        $I->see('Username cannot be blank.');
        $I->see('Password cannot be blank.');
    }

    public function loginWithWrongCredentials(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'user1',
            'LoginForm[password]' => 'wrong',
        ]);
        $I->expectTo('see validations errors');
        $I->see('Incorrect username or password.');
    }

    public function loginSuccessfully(\FunctionalTester $I)
    {
        $I->submitForm('#login-form', [
            'LoginForm[username]' => 'user1',
            'LoginForm[password]' => 'user1',
        ]);
        $I->see('Logout (user1)');
        $I->dontSeeElement('form#login-form');              
    }
}