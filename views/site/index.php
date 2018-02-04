<?php

use yii\widgets\LinkPager;
use yii\bootstrap\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $pages \yii\data\Pagination */
/* @var $models \app\models\Post[] */
/* @var $newPosts \app\models\Post[] */

$this->title = 'My Yii Application';
?>

<?php
$message = '';

foreach ($newPosts as $newPost) {
    echo \yii\bootstrap\Alert::widget([
        'body' => 'New post ' . Html::a('"' . Html::encode($newPost->name) . '"',['post/full', 'id' => $newPost->id],['class' => 'alert-link']),
        'options' => [
            'id' => 'news-' . $newPost->id,
            'class' => 'new-post alert-info',
            'data-post-id' => $newPost->id,
        ],
    ]);
}
?>

<div class="site-index">

    <div class="jumbotron">
        <h1>Hottest news!</h1>
    </div>

    <div class="body-content">

        <ul class="media-list">

            <?php foreach ($models as $model) : ?>

            <li class="media">
                <div class="media-left">
                    <?= Html::img($model->image, ['width' => 200]) ?>
                </div>
                <div class="media-body">
                    <?= Html::tag('h2', Html::encode($model->name), ['class' => 'media-heading']) ?>
                    <?= Html::tag('p', Html::encode($model->short_text)) ?>
                    <?= Html::a(Yii::t('app', 'Full text'), ['post/full', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                </div>
            </li>

            <?php endforeach; ?>

        </ul>

        <?= LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>

        <?= Html::beginForm([''],'get',['class'=>"form-inline"]); ?>
        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon" id="basic-addon1">Post per page</span>
                <?= Html::input('text', 'per-page', $pages->pageSize, ['class' => "form-control", 'aria-describedby' => "basic-addon1"]); ?>
            </div>
            <?= Html::input('hidden', 'page', $pages->page + 1, ['class' => "form-control"]); ?>
            <?= Html::input('submit', 'update', Yii::t('app', 'Update'), ['class' => "btn btn-primary"]); ?>
        </div>
        <?= Html::endForm(); ?>

    </div>
</div>

<?php
$markPostAsReadUrl = Url::to(['post/mark-as-read']);
$js = <<<JS
$('.new-post').on('closed.bs.alert', function () {
    $.ajax({
        url: '$markPostAsReadUrl',
        data: {
            id: $(this).data('post-id')
        }
    });
});
JS;
$this->registerJs($js)
?>
