<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use app\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php Modal::begin([
            'header' => '<h2>'.Yii::t('app', 'Create User').'</h2>',
            'toggleButton' => ['label' => Yii::t('app', 'Create User'), 'class' => 'btn btn-success'],
        ]);

        echo $this->render('create', [
            'model' => new User(),
        ]);

        Modal::end(); ?>

        <?php Modal::begin([
            'id' => 'edit-user-container',
            'header' => '<h2>'.Yii::t('app', 'Edit User').'</h2>',
            'toggleButton' => false,
            'clientOptions' => false,
        ]); ?>

        <?php Modal::end(); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'username',
            'email:email',
            'email_confirmed:boolean',
            'created',
            'last_visit',
            'active:boolean',

            [
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'update' => function($url, $model, $key) {
                        $title = Yii::t('yii', 'Update');
                        $options = [
                            'title' => $title,
                            'aria-label' => $title,
                            'data-pjax' => '0',
                            'data-target' => '#edit-user-container',
                            'data-toggle' => 'modal'
                        ];
                        $icon = Html::tag('span', '', ['class' => "glyphicon glyphicon-pencil"]);
                        return Html::a($icon,$url,$options);
                    }
                ],
            ],
        ],
    ]); ?>
</div>

<?php
$js = <<<JS
$(document).on('hidden.bs.modal', '.modal', function () {
  var modalData = $(this).data('bs.modal');

  // Destroy modal if has remote source – don't want to destroy modals with static content.
  if (modalData && modalData.options.remote) {
    // Destroy component. Next time new component is created and loads fresh content
    $(this).removeData('bs.modal');
    // Also clear loaded content, otherwise it would flash before new one is loaded.
    $(this).find(".modal-content").empty();
  }
});
JS;
$this->registerJs($js)
?>
