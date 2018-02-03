<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * RegisterForm is the model behind the register form.
 */
class UserProfileForm extends Model
{
    public $username;
    public $email;
    public $password;
    public $newPassword;
    public $repeatNewPassword;
    public $newsByEmail;
    public $newsByFlash;

    protected $user;

    function __construct(User $user, $config = [])
    {
        $this->user = $user;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->newsByEmail = $user->getNewsByEmail();
        $this->newsByFlash = $user->getNewsByFlash();
        parent::__construct($config);
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password'], 'required', 'message' => 'Current password required to save changes'],
            ['newPassword', 'compare', 'compareAttribute' => 'repeatNewPassword'],
            ['repeatNewPassword', 'compare', 'compareAttribute' => 'newPassword'],
            [['newsByEmail', 'newsByFlash'], 'boolean'],
        ];
    }

    public function getUser()
    {
        return $this->user;
    }

    public function save()
    {
        $user = $this->getUser();
        if ($user->validatePassword($this->password)) {
            if (!empty($this->newPassword)) {
                $user->setPassword($this->newPassword);
            }
            $user->setNewsByEmail($this->newsByEmail);
            $user->setNewsByFlash($this->newsByFlash);
            return $user->save(false);
        } else {
            $this->addError('password', 'Password is incorrect');
        }
        return false;
    }
}
