<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the register form.
 */
class RegisterForm extends Model
{
    public $username;
    public $password;
    public $repeatPassword;
    public $email;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['username', 'password', 'repeatPassword', 'email'], 'required'],
            ['username', 'unique', 'targetClass' => User::className()],
            ['repeatPassword', 'compare', 'compareAttribute' => 'password'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => User::className()],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function register()
    {
        if ($this->validate()) {
            if ($this->registerUser($this)) {
                return true;
            } else {
                $this->addError('username', 'Unknown error. Try to register later!');
                return false;
            }
        }
        return false;
    }

    /**
     * Create user
     *
     * @param self $form
     * @return true if success, or false on failure
     */
    public function registerUser(self $form)
    {
        $user = new User();
        return $user->register($form->username, $form->password, $form->email);
    }
}
