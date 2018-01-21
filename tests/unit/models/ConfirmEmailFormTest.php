<?php

namespace tests\models;

use app\models\ConfirmEmailForm;
use app\models\User;
use app\tests\fixtures\UserFixture;

class ConfirmEmailFormTest extends \Codeception\Test\Unit
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
        $user1 = $this->tester->grabFixture('user','user1');

        expect_not($user1->email_confirmed);

        $user1->generateConfirmationCode();
        $user1->save();

        $model = new ConfirmEmailForm([
            'confirmation_code' => $user1->getConfirmationCode(),
        ]);

        $this->assertEquals(true, $result = $model->confirmEmailAndLogin(), print_r([
            'result' => $result,
            'errors' => $model->getErrors(),
            'access_token' => $user1->getConfirmationCode(),
            'model.access_token' => $model->confirmation_code,
        ],true));

        expect($model->errors)->hasntKey('confirmation_code');
        expect_not(\Yii::$app->user->isGuest);

        $user = \Yii::$app->user->getIdentity();
        expect_not($user->getConfirmationCode());
        expect_that($user->email_confirmed);
    }

}
