<?php
/**
 * Created by PhpStorm.
 * User: shamgunov
 * Date: 16.01.2018
 * Time: 21:10
 */

namespace app\models;

use Yii;
use yii\base\Model;

class ConfirmEmailForm extends Model
{
    public $confirmation_code;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // access_token required
            [['confirmation_code'], 'required'],
            // password is validated by validatePassword()
            ['confirmation_code', 'validateConfirmationCode'],
        ];
    }

    public function validateConfirmationCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user) {
                $this->addError($attribute, 'Incorrect code.');
            }
        }
    }

    /**
     * Confirm email using the provided access_token.
     * Remove access token.
     * Logs in a user.
     * @return bool whether the user is logged in successfully
     */
    public function confirmEmailAndLogin()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->confirmEmail();
            $user->removeAccessToken();
            $user->save(); // TODO remove after implementing EVENT_AFTER_LOGIN in which "user.last_seen" is updated
            return Yii::$app->user->login($user); // TODO autologin by confirmation code convenient, but manual login is more safe?
        }
        return false;
    }

    /**
     * Finds user by [[access_token]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            // TODO To add checksum to a confirmation code and to check it before request to DB
            $this->_user = User::findIdentityByAccessToken($this->confirmation_code);
        }

        return $this->_user;
    }
}