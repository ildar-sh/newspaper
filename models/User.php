<?php

namespace app\models;

use yii\base\Event;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $password write-only password
 * @property string $password_hash
 * @property string $auth_key
 * @property string $access_token
 * @property string $email
 * @property bool $email_confirmed
 * @property bool $active
 * @property string $created
 * @property string $last_visit
 * @property string $receive_events_by_email_from_datetime
 * @property string $receive_events_by_alert_from_datetime
 * @property string $role
 *
 * @property Post[] $posts
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_USER = 'user';
    const ROLE_MANAGER = 'manager';
    const ROLE_ADMIN = 'admin';
    const DEFAULT_ROLE = self::ROLE_USER;

    public $roles = [
        self::ROLE_USER,
        self::ROLE_MANAGER,
        self::ROLE_ADMIN,
    ];

    /**
     * @event Event an event that is triggered when the user registered.
     */
    const EVENT_IS_REGISTERED = 'is_registered';

    /**
     * @event Event an event that is triggered when the user created by admin.
     */
    const EVENT_IS_CREATED_BY_ADMIN = 'is_created_by_admin';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['active','email_confirmed'], 'boolean'],
            [['username'], 'string', 'max' => 256],
            [['username'], 'unique'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['username','email','role'], 'required'],
            [['role'], 'string', 'max' => 15],
            ['role', 'default', 'value' => self::DEFAULT_ROLE],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'access_token' => Yii::t('app', 'Access Token'),
            'email' => Yii::t('app', 'Email'),
            'email_confirmed' => Yii::t('app', 'Email Confirmed'),
            'active' => Yii::t('app', 'Active'),
            'created' => Yii::t('app', 'Created'),
            'last_visit' => Yii::t('app', 'Last Visit'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosts()
    {
        return $this->hasMany(Post::className(), ['author_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->active()->one();
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['access_token' => $token])->active()->one();
    }

    /**
     * Finds active user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()->where(['username' => $username])->active()->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (isset($this->password_hash)) {
            return Yii::$app->security->validatePassword($password, $this->password_hash);
        } else {
            return false;
        }
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    public function removeAccessToken()
    {
        $this->access_token = null;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
                $this->email_confirmed = false;
                $this->generateConfirmationCode();
                $this->active = true;
            }
            return true;
        }
        return false;
    }

    public function confirmEmail()
    {
        $this->email_confirmed = true;
        $this->removeConfirmationCode();
    }

    public static function findByConfirmationCode($confirmationCode)
    {
        // TODO To add checksum to a confirmation code and to check it before request to DB
        return static::findIdentityByAccessToken($confirmationCode);
    }

    public function generateConfirmationCode()
    {
        $this->generateAccessToken();
    }

    public function getConfirmationCode()
    {
        return $this->access_token;
    }

    public function removeConfirmationCode()
    {
        $this->removeAccessToken();
    }

    public function register($username, $password, $email)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;

        return $this->save(false);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if ($insert) {
            if ($this->password_hash) {
                // user registered
                $this->trigger(self::EVENT_IS_REGISTERED, new Event());
            } else {
                // user created by admin
                $this->trigger(self::EVENT_IS_CREATED_BY_ADMIN, new Event());
            }
        }
    }

    public function getNewsByFlash()
    {
        $newsByFlashFrom = $this->receive_events_by_alert_from_datetime;
        return !is_null($newsByFlashFrom);
    }

    public function setNewsByFlash($value)
    {
        if ($value) {
            $now = new \DateTime();
            $this->receive_events_by_alert_from_datetime = $now->format(\DateTime::ISO8601);
        }  else {
            $this->receive_events_by_alert_from_datetime = null;
        }
    }

    public function getNewsByEmail()
    {
        $newsByEmailFrom = $this->receive_events_by_email_from_datetime;
        return !is_null($newsByEmailFrom);
    }

    public function setNewsByEmail($value)
    {
        if ($value) {
            $now = new \DateTime();
            $this->receive_events_by_email_from_datetime = $now->format(\DateTime::ISO8601);
        }  else {
            $this->receive_events_by_email_from_datetime = null;
        }
    }
}
