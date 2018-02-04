<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "post".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $image
 * @property string $short_text
 * @property string $long_text
 * @property bool $active
 * @property string $created
 * @property int $author_id
 *
 * @property User $author
 */
class Post extends \yii\db\ActiveRecord implements CanBeViewed
{
    /**
     * @var UploadedFile
     */
    public $image_file;

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
            [['active'], 'boolean'],
            [['name', 'description'], 'string', 'max' => 256],
            [['image_file'], 'file', 'extensions' => ['png', 'jpg'], 'mimeTypes' => ['image/jpeg', 'image/png']],
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
            'image_file' => Yii::t('app', 'Image'),
            'short_text' => Yii::t('app', 'Short Text'),
            'long_text' => Yii::t('app', 'Long Text'),
            'active' => Yii::t('app', 'Active'),
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

    /**
     * @param bool $insert whether this method called while inserting a record.
     * If `false`, it means the method is called while updating a record.
     * @return bool whether the insertion or updating should continue.
     * If `false`, the insertion or updating will be cancelled.
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->author_id = Yii::$app->user->getId(); // todo move code to behavior

        if ($this->image_file) {
            // todo path settings move to config
            $filePath = 'uploads/' . Yii::$app->security->generateRandomString() . '.' . $this->image_file->extension;
            // todo resize image to standard size
            $isImageSaved = $this->image_file->saveAs($filePath);
            if ($isImageSaved) {
                // delete old file
                unlink($this->image);
                $this->image = $filePath;
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool whether the record should be deleted.
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $isImageFileDeleted = unlink($this->image);

        if ($isImageFileDeleted) {
            return true;
        } else {
            $this->addError('image','Cant delete associated file');
            // If can't delete associated file< do not delete model
            return false;
        }
    }

    public function markAsViewed($user_id, $transport)
    {
        $postView = new PostView();
        $postView->post_id = $this->id;
        $postView->user_id = $user_id;
        $postView->transport = $transport;
        $postView->save(false);
    }

    public function getNewFor($userId, $transport,\DateTime $dateFrom, $limit)
    {
        $subQuery = PostView::find()->select('post_id')->where([
            'user_id' => $userId,
            'transport' => $transport
        ]);
        return $this->find()->active()->newerThan($dateFrom)->andWhere(['not in', 'id', $subQuery])->limit($limit)->all();
    }
}
