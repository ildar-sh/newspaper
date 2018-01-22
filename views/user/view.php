<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php Modal::begin([
            'header' => '<h2>'.Yii::t('app', 'Create User').'</h2>',
            'toggleButton' => ['label' => Yii::t('app', 'Update'), 'class' => 'btn btn-primary'],
        ]);

        echo $this->render('update', [
            'model' => $model,
        ]);

        Modal::end(); ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'password_hash',
            'email:email',
            'email_confirmed:boolean',
            'active:boolean',
            'created',
            'last_visit',
        ],
    ]) ?>

</div>
