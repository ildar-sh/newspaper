<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Posts');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Post'), ['create'], ['class' => 'btn btn-success load-create-form-to-edit-post-container']) ?>
        <?php Modal::begin([
            'id' => 'edit-post-modal',
            'toggleButton' => false,
        ]); ?>
            <div id="edit-post-container"></div>

        <?php Modal::end(); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'name',
            'description',
            //'image',
            //'short_text:ntext',
            //'long_text:ntext',
            [
                'attribute' => 'active',
                'format' => 'boolean',
                'content' => function ($model, $key, $index, $column) {
                    $options = [
                        'class' => 'post-active',
                        'type' => 'checkbox',
                        'name' => 'active',
                        'checked' => $model->active,
                        'value' => $key,
                    ];
                    return Html::tag('input', null, $options);
                }
            ],
            //'active:boolean',
            'created',
            //'author_id',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function($url, $model, $key) {
                        $title = Yii::t('yii', 'Update');
                        $options = [
                            'class' => 'load-update-form-to-edit-post-container',
                            'title' => $title,
                            'aria-label' => $title,
                            'data-pjax' => '0',
                        ];
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                        return Html::a($icon,$url,$options);
                    }
                ],
                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        return \Yii::$app->user->can('updatePost', ['post' => $model]);
                    },
                    'delete' => function ($model, $key, $index) {
                        return \Yii::$app->user->can('deletePost', ['post' => $model]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>

<?php
$activatePostUrl = Url::to(['activate']);
$js = <<<JS
$(document).on('click', 'a.load-update-form-to-edit-post-container', function () {
    $.ajax({
        url: this.href,
        success: function( data ) {
                $('#edit-post-container').html(data);
                $('#edit-post-modal').modal('show');
            },
        error: function() {
                alert('Check connection to the server');
            }
    });
    return false;
});

$(document).on('click', '.load-create-form-to-edit-post-container', function () {
    $.ajax({
        url: this.href,
        success: function( data ) {
                $('#edit-post-container').html(data);
                $('#edit-post-modal').modal('show');
            },
        error: function() {
                alert('Check connection to the server');
            }
    });
    return false;
});

$(document).on('change', ':checkbox.post-active', function () {
    var previousState = !this.checked;
    var checkbox = this;
    $.ajax({
        url: '$activatePostUrl',
        data: {
            id : this.value,
            status : this.checked
        },
        error: function() {
                checkbox.checked = previousState;
                alert('Check connection to the server');
            }
        }
    );
});
JS;
$this->registerJs($js)
?>