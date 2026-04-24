<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php
    $cemeteries = \app\models\Cemetery::find()->all();
    $items = [];
    foreach ($cemeteries as $cemetery) {
        $items[$cemetery->id] = $cemetery->name;
    }
    ?>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cemetery_id')->dropDownList($items) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'svazka')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year1')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'records')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'per_page')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(app\models\Book::getStatuses()) ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
