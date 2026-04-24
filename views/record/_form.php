<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\HelperLevoshkin;

/** @var yii\web\View $this */
/** @var app\models\Record $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php
$images_list = [];
if ($model->filename)
    $images_list = \app\models\HelperImg::getImages($model->filename, 8888, 8888);
$images_list_short = [];
$images_short_to_path = [];
foreach ($images_list as $img) {
    $images_list_short[] = $img['src3'];
    $images_short_to_path[$img['src3']] = $img;

    //print_r($img);
    //exit;
}

$f_img_name = '';

if ($model->filename) {
    $f_img = $model->filename;
    $f_img_name = explode('/', str_replace("\\", "/", $f_img));
    $f_img_name = end($f_img_name);
}
//echo $f_img_name;
//exit;
?>

<div class="record-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container">

        <div class="row">
            <div class="col-sm-5">
                <?= $form->field($model, 'filename')->textInput(['maxlength' => true, 'readonly' => !$is_create]) ?>
            </div>
            <div class="col-sm-3">
                <label for ='select_fname'>Выбрать файл</label>
                <select onchange='change_select_img()' id="select_fname" class="form-control" >
                    <?php
                    foreach ($images_list_short as $short):
                        $checked = '';
                        if ($short == $f_img_name)
                            $checked = "selected checked ='checked' ";
                        ?>
                        <option <?= $checked ?> value='<?= $images_short_to_path[$short]['src2'] ?>' ><?= $short ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if (!$is_create): ?>
                <div  class="col-sm-2">    
                    <a style='float:right;display:none;' id="open_img" href="lotus://<?= $model->id ?>" ><img style=''  src="/img/view.png" /></a>
                </div>
                <div class="col-sm-2"  >
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>           
            <?php endif; ?>

        </div>
        <div class="row">
            <?php //$form->field($model, 'book_id')->textInput()    ?>
            <div class="col-sm-2">
                <?= $form->field($model, 'numReg')->textInput() ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'numLiteral')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>

                <div class='valid-feedback fio_label' ONCLICK='javascript:help_fio();'></div>

            </div>

            <div class="col-sm-1">
                <?= $form->field($model, 'age')->textInput() ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'death_date')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'rip_date')->textInput(['maxlength' => true]) ?>
            </div>

        </div>

        <div class="row">
            <?php //$form->field($model, 'book_id')->textInput()      ?>

            <div class="col-sm-2">
                <?= $form->field($model, 'docnum')->textInput(['maxlength' => true]) ?>



            </div>            <div class="col-sm-2">
                <?= $form->field($model, 'zags')->textInput() ?>
                <?php
                if ($model->zags) {
                    $reg = HelperLevoshkin::region($model->zags);
                    if (($reg[1] < 100) and ($reg[1] > 80)) {
                        echo "<div style='display:block;' class='valid-feedback region_valid' ONCLICK='javascript:help_region();'>$reg[0]</div>";
                    }
                }
                ?>


            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'area_num')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'row_num')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'rip_num')->textInput(['maxlength' => true]) ?>
            </div>

        </div>



        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'relative_fio')->textInput(['maxlength' => true]) ?>

                <div class='valid-feedback relative_fio_label' ONCLICK='javascript:help_relative_fio();'></div>

            </div>
            <div class="col-sm-6">
                <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'rip_style')->dropDownList(\app\models\Record::ripStyleTypes()) ?>
                <input type="hidden" id='pageNum' name='pageNum' value='1' />
            </div>
        </div>


        <?php if ($is_create): ?>
            <div class="row">
                <div class="col-sm"  >
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>           
            </div>           
        <?php endif; ?>

    </div>

    <?php ActiveForm::end(); ?>

</div>
