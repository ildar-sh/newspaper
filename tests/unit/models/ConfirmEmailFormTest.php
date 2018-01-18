<?php

namespace tests\models;

use app\models\ConfirmEmailForm;
use app\models\User;
use app\tests\fixtures\UserFixture;

class ConfirmEmailFormTest extends \Codeception\Test\Unit
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

    protected function _after()
    {
        \Yii::$app->user->logout();
    }

    public function testEmptyCode()
    {
        $model = new ConfirmEmailForm();

        expect_not($model->confirmEmailAndLogin());
        expect_that(\Yii::$app->user->isGuest);
        expect($model->errors)->hasKey('confirmation_code');
    }

    public function testIncorrectCode()
    {
        $model = new ConfirmEmailForm([
            'confirmation_code' => 'abvgd'
        ]);

        expect_not($model->confirmEmailAndLogin());
        expect_that(\Yii::$app->user->isGuest);
        expect($model->errors)->hasKey('confirmation_code');
    }

    public function testCorrectCode()
    {
        /**
         * @var $user1 User
         */
        $user1 = $this->getModule('Yii2')->grabFixture('user','user1');

        expect_not($user1->email_confirmed);

        $user1->generateAccessToken();
        $user1->save();

        $model = new ConfirmEmailForm([
            'confirmation_code' => $user1->access_token,
        ]);

        $this->assertEquals(true, $result = $model->confirmEmailAndLogin(), print_r([
            'result' => $result,
            'errors' => $model->getErrors(),
            'access_token' => $user1->access_token,
            'model.access_token' => $model->confirmation_code,
        ],true));

        expect($model->errors)->hasntKey('confirmation_code');
        expect_not(\Yii::$app->user->isGuest);

        $user = \Yii::$app->user->getIdentity();
        expect_not($user->access_token);
        expect_that($user->email_confirmed);
    }

}
