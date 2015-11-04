<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nome')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'senha')->textInput(['maxlength' => true]) ?>

    <?php //echo  $form->field($model, 'cracha')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'instituicao')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'tipoUsuario')->textInput() ?>

    <?= $form->field($model, 'notificarViaEmail')->textInput() ?>

    <?php //echo $form->field($model, 'authKey')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'accessToken')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>