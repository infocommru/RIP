<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\HelperLevoshkin;
use app\models\HelperImg;
use yii\helpers\StringHelper;

/** @var yii\web\View $this
* @var app\models\Record $model
* @var yii\widgets\ActiveForm $form
* @var bool $is_create
*/

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
                <select id="select_fname" class="form-control"></select>
            </div>
            <?php if (!$is_create): ?>
                <div class="col-sm-2 d-flex align-items-center">
                    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
                </div>
            <?php endif; ?>

        </div>
        <div class="row">
            <div class="col-sm-2">
                <?= $form->field($model, 'numReg')->textInput() ?>
            </div>
            <div class="col-sm-2">
                <?= $form->field($model, 'numLiteral')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3 parent-speller">
                <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
                <div class="speller mb-2" style="color: green; cursor: pointer;"></div>
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
            <div class="col-sm-2">
                <?= $form->field($model, 'docnum')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-2 parent-speller">
                <?= $form->field($model, 'zags')->textInput() ?>
                <div class="speller mb-2" style="color: green; cursor: pointer;"></div>
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
            <div class="col-sm-4 parent-speller">
                <?= $form->field($model, 'relative_fio')->textInput(['maxlength' => true]) ?>
                <div class="speller mb-2" style="color: green; cursor: pointer;"></div>
            </div>
            <div class="col-sm-6 parent-speller">
                <?= $form->field($model, 'comment')->textInput(['maxlength' => true]) ?>
                <div class="speller mb-2" style="color: green; cursor: pointer;"></div>
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let timer;

        document.querySelectorAll(".parent-speller").forEach(el => {
            setSpellValue(el.querySelector('input'), el.querySelector('.speller'));
        });

        function setSpellValue(recordDom, spellerDom)
        {
            if(recordDom.value){
                speller(recordDom.value).then(result => {
                    if(result && result !== recordDom.value)
                        spellerDom.textContent = result;
                    else
                        spellerDom.textContent = '';
                });
            }
            else
                spellerDom.textContent = '';

        }

        async function speller(text) {
            let text_array = text.trim().split(/\s+/);
            const positions = [...text.matchAll(/\S+/g)].map(m => m.index);

            try {
                const response = await fetch(
                    "https://speller.yandex.net/services/spellservice.json/checkText",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            text: text,
                            lang: "ru",
                            options: 0
                        })
                    }
                );

                if (!response.ok) {
                    throw new Error(`Speller HTTP ${response.status}`);
                }

                const result = await response.json();

                result.forEach(index => {
                    if (index.s && index.s.length > 0) {
                        text_array[positions.indexOf(index.pos)] = index.s[0];
                    }
                });

                return text_array.join(" ");

            } catch (error) {
                console.error(error);
                return false;
            }
        }

        document.querySelectorAll(".speller").forEach(el => {
            el.addEventListener('click', (e) => {
                const inputDom = e.target.parentElement
                    .querySelector('input');

                inputDom.value = el.textContent;
                el.textContent = '';
            });
        });

        document.querySelectorAll(".parent-speller").forEach(el => {
            el.querySelector('input').addEventListener('input', (e) => {
                clearTimeout(timer);

                timer = setTimeout(async () => {
                        const spellerDom = e.target
                            .closest('.parent-speller')
                            .querySelector('.speller');

                        setSpellValue(e.target, spellerDom);
                }, 500);
            });
        });
    });
</script>