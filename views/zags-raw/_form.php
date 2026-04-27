<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ZagsRaw $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="zags-raw-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'zags_id')->dropDownList(\app\models\Helper::regionsWithIds()) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cnt')->textInput() ?>

    <?php //$form->field($model, 'percent')->hiddenInput(['label' => '']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
