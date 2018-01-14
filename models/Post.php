<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $short_text
 * @property string $long_text
 * @property int $status
 * @property string $created
 * @property int $author_id
 *
 * @property User $author
 */
class Post extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['short_text', 'long_text'], 'string'],
            [['status', 'author_id'], 'default', 'value' => null],
            [['status', 'author_id'], 'integer'],
            [['created'], 'safe'],
            [['author_id'], 'required'],
            [['name', 'description'], 'string', 'max' => 256],
            [['image'], 'string', 'max' => 1024],
            [['author_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['author_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'image' => Yii::t('app', 'Image'),
            'short_text' => Yii::t('app', 'Short Text'),
            'long_text' => Yii::t('app', 'Long Text'),
            'status' => Yii::t('app', 'Status'),
            'created' => Yii::t('app', 'Created'),
            'author_id' => Yii::t('app', 'Author ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::className(), ['id' => 'author_id']);
    }

    /**
     * @inheritdoc
     * @return PostQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PostQuery(get_called_class());
    }
}
