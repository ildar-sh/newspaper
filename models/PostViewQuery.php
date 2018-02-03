<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[PostView]].
 *
 * @see PostView
 */
class PostViewQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return PostView[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return PostView|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
