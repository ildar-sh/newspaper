<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post_view".
 *
 * @property int $id
 * @property int $user_id
 * @property int $post_id
 * @property string $transport
 * @property string $created
 *
 * @property Post $post
 * @property User $user
 */
class PostView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_view';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'post_id'], 'default', 'value' => null],
            [['user_id', 'post_id'], 'integer'],
            [['created'], 'safe'],
            [['transport'], 'string', 'max' => 127],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => Post::className(), 'targetAttribute' => ['post_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'post_id' => 'Post ID',
            'transport' => 'Transport',
            'created' => 'Created',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(Post::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return PostViewQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostViewQuery(get_called_class());
    }
}
