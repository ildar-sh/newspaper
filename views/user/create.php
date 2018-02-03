<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<?php Pjax::begin(['enablePushState' => false]); ?>

<div class="create-user-form">

    <?php $form = ActiveForm::begin(['id' => 'create-user-form', 'action' => ['user/create'], 'options' => ['data-pjax' => '']]); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList(array_combine($model->roles, $model->roles)) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php Pjax::end(); ?>