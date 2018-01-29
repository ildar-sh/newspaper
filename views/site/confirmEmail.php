<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ConfirmEmailForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Confirm email';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-confirm-email">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the confirmation code to confirm email:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'register-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
            'labelOptions' => ['class' => 'col-lg-1 control-label'],
        ],
    ]); ?>

        <?= $form->field($model, 'confirmation_code')->textInput(['autofocus' => true]) ?>

        <?php if($model->scenario == \app\models\ConfirmEmailForm::SCENARIO_CONFIRM_AND_SET_PASSWORD) { ?>

            <?= $form->field($model, 'password')->passwordInput() ?>

            <?= $form->field($model, 'repeatPassword')->passwordInput() ?>

        <?php } ?>

        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Confirm', ['class' => 'btn btn-primary', 'name' => 'register-button']) ?>
            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>
