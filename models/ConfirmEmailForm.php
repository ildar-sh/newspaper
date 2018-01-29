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
    const SCENARIO_CONFIRM = 'confirm';
    const SCENARIO_CONFIRM_AND_SET_PASSWORD = 'confirmAndSetPassword';

    public $confirmation_code;
    public $password;
    public $repeatPassword;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // access_token required
            [['confirmation_code','password','repeatPassword'], 'required'],
            // password is validated by validatePassword()
            ['confirmation_code', 'validateConfirmationCode'],
            ['repeatPassword', 'compare', 'compareAttribute' => 'password'],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_CONFIRM => ['confirmation_code'],
            self::SCENARIO_CONFIRM_AND_SET_PASSWORD => ['confirmation_code','password','repeatPassword']
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
            if ($user) {
                if ($user->password_hash) {
                    $user->confirmEmail();
                } else {
                    if($this->password) {
                        $user->setPassword($this->password);
                        $user->confirmEmail();
                    } else {
                        return false;
                    }
                }


                //$user->save(); // TODO remove after implementing EVENT_AFTER_LOGIN in which "user.last_seen" is updated
                return Yii::$app->user->login($user); // TODO autologin by confirmation code convenient, but manual login is more safe?
            } else {
                return false;
            }
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
            $this->_user = User::findByConfirmationCode($this->confirmation_code);
        }

        return $this->_user;
    }
}