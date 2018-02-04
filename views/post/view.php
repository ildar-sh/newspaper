<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Posts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], [
            'class' => 'btn btn-primary',
            'style' => Yii::$app->user->can('updatePost',['post' => $model]) ? "" : "display: none;",
        ]) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
            'style' => Yii::$app->user->can('deletePost',['post' => $model]) ? "" : "display: none;",
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            //'image:image',
            [
                'attribute'=>'image',
                'format' => ['image',['width'=>300]],
            ],
            'short_text:ntext',
            'long_text:ntext',
            'active:boolean',
            'created',
            'author_id',
        ],
    ]) ?>

</div>
