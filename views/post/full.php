<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Post */

$this->title = $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-full">

    <?= Html::tag('h1', Html::encode($model->name), ['class' => 'media-heading']) ?>

    <?= Html::img($model->image, ['width' => 768]) ?>

    <?= Html::tag('p', Html::encode($model->long_text)) ?>

</div>

<?php
$markPostAsReadUrl = Url::to(['mark-as-read', 'id' => $model->id]);
$js = <<<JS
$.ajax({
    url: '$markPostAsReadUrl'
});
JS;
$this->registerJs($js)
?>