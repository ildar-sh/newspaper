<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\UserProfileForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Profile';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>User profile settings:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'profile-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'username')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'email')->textInput(['readonly' => true]) ?>

        <?= $form->field($model, 'newPassword')->passwordInput() ?>
        <?= $form->field($model, 'repeatNewPassword')->passwordInput() ?>

        <?= $form->field($model, 'newsByEmail')->checkbox() ?>
        <?= $form->field($model, 'newsByFlash')->checkbox() ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">

            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'save-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>