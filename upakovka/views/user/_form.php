<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lastname')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'middlename')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'position')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'role')->dropDownList(User::roleList()) ?>
<?php if (0):?>
    <?= $form->field($model, 'cemetery_id')->dropDownList(app\models\Helper::getCemeteryList()) ?>
    <?php if($model->cemetery_id):?>
<?= $form->field($model, 'book_id')->dropDownList(app\models\Helper::getBookList($model->cemetery_id)) ?>
<?php endif;?>
    <?php endif;?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
