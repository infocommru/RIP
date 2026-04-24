<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\HelperLevoshkin;
use app\models\Cemetery;
use app\models\Helper;

$this->title = 'Поиск по захоронениям г. Санкт-Петербурга';

/** @var yii\web\View $this */
/** @var app\models\Record $model */

/** @var yii\widgets\ActiveForm $form */
function get_input_value($txt) {
    if (!empty($_GET[$txt])) {
        return strtr($_GET[$txt], ['"' => '']);
    }

    return "";
}

function echo_select_soderzit($name) {
    $r = "<label for = \"$name\">Вхождение</label>";
    //$r = '';
    $r .= "<select id = \"$name\" name='$name' class=\"form-control\">";

    $values = [
        1 => 'равно',
        2 => 'содержит',
        3 => 'начинается с',
        4 => 'заканчивается на',
    ];

    foreach ($values as $k => $v) {
        $selected = '';
        if (isset($_GET[$name])) {
            if ($_GET[$name] == $k)
                $selected = "selected";
        } else {
            if ($k == 1)
                $selected = "selected";
        }

        $r .= "<option $selected value='$k'>" . $v . "</option>";
    }

    $r .= "</select>";
    return $r;
}
?>
<form method='get' onsubmit="return validateForm(true)">
    <h2>Поиск по захоронениям г. Санкт-Петербурга</h2>
    <hr />
    <h4>Основные параметры</h4>
    <div class="search-form">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    <label for='fam'>Фамилия</label>
                    <input value="<?= get_input_value("fam") ?>" type="text" class='form-control' name='fam' id='fam' />
                </div>
                <div class="col-sm-1">
                    <?= echo_select_soderzit('fam_cont') ?>
                </div>
                <div class="col-sm-2">
                    <label for='nam'>Имя</label>
                    <input value="<?= get_input_value("nam") ?>" type="text" class='form-control' name='nam' id='nam' />
                </div>
                <div class="col-sm-1">
                    <?= echo_select_soderzit('nam_cont') ?>
                </div>
                <div class="col-sm-2">
                    <label for='ot'>Отчество</label>
                    <input value="<?= get_input_value("ot") ?>" type="text" class='form-control' name='ot' id='ot' />

                </div>
                <div class="col-sm-1">
                    <?= echo_select_soderzit('ot_cont') ?>

                </div>

                <div class="col-sm-2">
                    <label for='regnum'>Номер записи</label>
                    <input value="<?= get_input_value("regnum") ?>" type="text" class='form-control' name='regnum' id='regnum' />
                </div>

                <div class="col-sm-1">
                    <?= echo_select_soderzit('rg_cont') ?>

                </div>


            </div>

            <div class='row'>



                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="cemetery">Кладбище</label>
                        <select id="cemetery" name='cemetery' class="form-control">
                            <option value='0'>-</option>
                            <?php
                            $c_id = 0;
                            if ((isset($_GET['cemetery'])) && ($_GET['cemetery'] != '0')) {
                                $c_id = intval($_GET['cemetery']);
                            }

                            $cemeteries = Cemetery::find()->all();
                            foreach ($cemeteries as $cemetery) {
                                $selected = '';
                                if ($cemetery->id == $c_id) {
                                    $selected = "selected";
                                }
                                echo "<option $selected value='" . $cemetery->id . "'>" . $cemetery->name . "</option>";
                            }
                            ?>

                        </select>
                    </div>
                </div>

                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="rip_style">Захоронение</label>
                        <select id="rip_style" name='rip_style' class="form-control">
                            <option value='0'>-</option>
                            <?php
                            $r_id = 0;
                            if ((isset($_GET['rip_style'])) && ($_GET['rip_style'] != '0')) {
                                $r_id = intval($_GET['rip_style']);
                            }

                            $riplist = \app\models\Record::ripStyleTypes();
                            foreach ($riplist as $rip_id => $rip_val) {
                                $selected = '';
                                if ($rip_id == $r_id) {
                                    $selected = "selected";
                                }
                                echo "<option $selected value='" . $rip_id . "'>" . $rip_val . "</option>";
                            }
                            ?>

                        </select>
                    </div>
                </div>




                <div class="col-sm-2">
                    <br />
                    <div class="form-group">

                        <input  <?php if (isset($_GET['unknown'])) echo 'checked'; ?> type='checkbox' name='unknown' id='unknown' />
                        <label style='color:#cc0000' for="unknown">Неизвестный</label>
                    </div>
                </div>



                <div class="col-sm-2">
                    <label for="unknown_number">Номер неизвестного</label>
                    <input value="<?= get_input_value("unknown_number") ?>"  type=text class='form-control' id='unknown_number' name='unknown_number' />
                </div>



                <div class="col-sm-4">
                    <br />
                    <a href="javascript:extended_search();"><input type='checkbox' id='ext_search' onchange="javascript:extended_search();" name='ext_search'>Дополнительные параметры</a>

                </div>

            </div>

            <div class="row">

                <div class="col-sm-2">
                    <label for="age">Возраст</label>
                    <input value="<?= get_input_value("age") ?>"  type=text class='form-control' id='age' name='age' />
                </div>

                <div class="col-sm-2">
                    <label for="age_cmp">Сравнение</label>

                    <?php
                    $selected = ['selected', '', ''];
                    if (isset($_GET['age_cmp'])) {
                        switch (intval($_GET['age_cmp'])) {
                            case 2:
                                $selected = ['', 'selected', ''];
                                BREAK;
                            case 3:
                                $selected = ['', '', 'selected'];
                                BREAK;
                        }
                    }
                    ?>


                    <select id="age_cmp" name='age_cmp' class="form-control">
                        <option <?= $selected[0] ?> value='1'>Равно</option>
                        <option <?= $selected[1] ?> value='2'>Меньше</option>
                        <option <?= $selected[2] ?> value='3'>Больше</option>

                    </select>
                </div>


                <div class="col-sm-2">
                    <label for="dead_year">Год смерти</label>
                    <input value="<?= get_input_value("dead_year") ?>" type=text class='form-control' id='dead_year' name='dead_year' />
                </div>

                <?php
                $selected = ['selected', '', ''];
                if (isset($_GET['dead_year_cmp'])) {
                    switch (intval($_GET['dead_year_cmp'])) {
                        case 2:
                            $selected = ['', 'selected', ''];
                            BREAK;
                        case 3:
                            $selected = ['', '', 'selected'];
                            BREAK;
                    }
                }
                ?>

                <div class="col-sm-2">
                    <label for="dead_year_cmp">Сравнение</label>
                    <select id="dead_year_cmp" name='dead_year_cmp' class="form-control">
                        <option  <?= $selected[0] ?> value='1'>Равно</option>
                        <option  <?= $selected[1] ?> value='2'>Меньше</option>
                        <option  <?= $selected[2] ?> value='3'>Больше</option>

                    </select>
                </div>



                <div class="col-sm-4">
                    <button type="submit" class="btn btn-primary btn-lg btn-block search_btn">Найти</button>            
                </div>




            </div>

            <hr />


            <div id='additional_search_params' >
                <h4>Дополнительные параметры</h4>
                <div class="row">

                    <div class="col-sm-2">
                        <label for="rip_year">Год захоронения</label>
                        <input value="<?= get_input_value("rip_year") ?>" type=text class='form-control' id='rip_year' name='rip_year' />
                    </div>


                    <?php
                    $selected = ['selected', '', ''];
                    if (isset($_GET['rip_year_cmp'])) {
                        switch (intval($_GET['rip_year_cmp'])) {
                            case 2:
                                $selected = ['', 'selected', ''];
                                BREAK;
                            case 3:
                                $selected = ['', '', 'selected'];
                                BREAK;
                        }
                    }
                    ?>



                    <div class="col-sm-2">
                        <label for="rip_year_cmp">Сравнение</label>
                        <select id="rip_year_cmp" name='rip_year_cmp' class="form-control">
                            <option <?= $selected[0] ?> value='1'>Равно</option>
                            <option <?= $selected[1] ?> value='2'>Меньше</option>
                            <option <?= $selected[2] ?> value='3'>Больше</option>

                        </select>
                    </div>







                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="zags">ЗАГС</label>
                            <select id="zags" name='zags' class="form-control">
                                <option value='-1'>-</option>
                                <?php
                                $regions = Helper::regions();
                                $z_id = -1;
                                if ((isset($_GET['zags'])) && ($_GET['zags'] != '-1'))
                                    $z_id = intval($_GET['zags']);

                                foreach ($regions as $index => $region) {
                                    $selected = '';
                                    if ($z_id == $index)
                                        $selected = 'selected';
                                    echo "<option $selected value='$index'>$region</option>";
                                }
                                ?>

                            </select>
                        </div>
                    </div>



                    <div class="col-sm-4">
                        <div class="form-group">
                            <label for="docnum">Номер документа</label>
                            <input value="<?= get_input_value("docnum") ?>" type=text class='form-control' id='docnum' name='docnum' />
                        </div>
                    </div>



                </div>

                <div class="row">
                    <div class="col-sm-2">
                        <label for="areanum">Номер участка</label>
                        <input value="<?= get_input_value("areanum") ?>" type=text class='form-control' id='areanum' name='areanum' />
                    </div>
                    <div class="col-sm-2">
                        <br />
                        <input  <?php if (isset($_GET['area_cont'])) echo 'checked'; ?> type="checkbox" name='area_cont' id='area_cont' />
                        <label for='area_cont'>Содержит</label>
                    </div>
                    <div class="col-sm-2">
                        <label for="rownum">Номер ряда</label>
                        <input value="<?= get_input_value("rownum") ?>" type=text class='form-control' id='rownum' name='rownum' />
                    </div>

                    <div class="col-sm-2">
                        <br />
                        <input  <?php if (isset($_GET['row_cont'])) echo 'checked'; ?> type="checkbox" name='row_cont' id='row_cont' />
                        <label for='row_cont'>Содержит</label>
                    </div>

                    <div class="col-sm-2">
                        <label for="ripnum">Номер могилы</label>
                        <input value="<?= get_input_value("ripnum") ?>" type=text class='form-control' id='ripnum' name='ripnum' />
                    </div>

                    <div class="col-sm-2">
                        <br />
                        <input  <?php if (isset($_GET['rip_cont'])) echo 'checked'; ?> type="checkbox" name='rip_cont' id='rip_cont' />
                        <label for='rip_cont'>Содержит</label>
                    </div>

                </div>


                <div class="row">
                    <div class="col-sm-12">
                        <label for="rel">Родственники</label>
                        <input value="<?= get_input_value("rel") ?>" type=text class='form-control' id='rel' name='rel' />
                    </div>
                </div>
            </div>

            <hr />

            <?php
            if ($search_data) {
                echo Yii::$app->controller->renderPartial('_search_result',
                        ['data' => $search_data]);
            } else if (isset($_GET['fam'])) {
                echo "<h5>По вашему запросу ничего не найдено, попробуйте уточнить критерии поиска</h5>";
            }
            ?>


        </div>
    </div>
    <input type='hidden' id='pager' name='pager'>
</form>

<script>


    document.addEventListener("DOMContentLoaded", function (event) {
        jQuery('#tabs').tabs();
        var availableTags = [
            "неизвестный",
            "неизвестная",
            "н/м",
            "н/ж",
        ];
        $("#fam").autocomplete({
            source: availableTags
        });
<?php if (isset($_GET['ext_search'])): ?>
            extended_search();
<?php endif; ?>
    });
    function  validateForm(show_alert) {
        var nam = jQuery('#nam').val();
        var fam = jQuery('#fam').val();
        var ot = jQuery('#ot').val();
        var age = jQuery('#age').val();
        var dead_year = jQuery('#dead_year').val();
        var cemetery = jQuery('#cemetery').val();
        var unk = jQuery('#unknown').is(':checked');
        var regnum = jQuery('#regnum').val();

        //alert(cemetery);
        if ((!nam) && (!regnum) && (!fam) && (!ot) && (!age) && (!dead_year) && (!unk) && (cemetery == '0')) {
            if (show_alert)
                alert("Требуются данные основного поиска")

            return false;
        }
        return true;
    }


    function extended_search() {
        if (jQuery("#additional_search_params").is(":visible")) {
            //alert(1)
            jQuery("#additional_search_params").fadeOut(100);
            jQuery("#ext_search").prop("checked", !true);

        } else {
            if (!validateForm(true)) {
                return;
            }


            jQuery("#ext_search").prop("checked", true);
            jQuery("#additional_search_params").fadeIn(300);
        }
    }

    function vopros(record_id) {
        jQuery.get("/web/search/vopros", {"record_id": record_id}, function (data) {
            alert("Данные отправлены на уточнение");
            jQuery('#vopros' + record_id).fadeOut(400);
        });
        //alert(record_id);
    }


    function next_page(cemetery_id, page) {
        var val = jQuery('#pager').val();
        //alert(val);
        if (val)
            val += ";";
        val += cemetery_id + "," + page;
        jQuery('#pager').val(val)
        jQuery('.search_btn').click()


    }

</script>
