<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Record2 $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="record2-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'book_id')->textInput() ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'numReg')->textInput() ?>

    <?= $form->field($model, 'numLiteral')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'age')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'death_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rip_date')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'docnum')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zags')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'riper')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'area_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'row_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'rip_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'relative_fio')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'filename')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'rip_style')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
