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

    protected $user;

    function __construct(User $user, $config = [])
    {
        $this->user = $user;
        $this->email = $user->email;
        $this->username = $user->username;
        parent::__construct($config);
    }


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['password'], 'required', 'message' => 'Current password required to save changes'],
            ['repeatNewPassword', 'compare', 'compareAttribute' => 'newPassword'],
        ];
    }

    public function getUser()
    {
        return $this->user;
    }
}
