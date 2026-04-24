<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BookUpload $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-upload-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cemetery_id')->textInput() ?>

    <?= $form->field($model, 'add_at')->textInput() ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
