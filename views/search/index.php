<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\HelperLevoshkin;
use app\models\Cemetery;
use app\models\Helper;

use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Url;

/** @var yii\web\View $this
 * @var app\models\Record $model
 * @var yii\widgets\ActiveForm $form
 * @var yii\web\View $this
 * @var false|array<string, mixed> $search_data
*/
$user = app\models\User::findIdentity(Yii::$app->user->id);
$this->title = 'Поиск по захоронениям г. Санкт-Петербурга';

/**
 * @param string $txt
 * @return string
 */
function get_input_value($txt) {
    if (!empty($_GET[$txt])) {
        return strtr($_GET[$txt], ['"' => '']);
    }

    return "";
}

/**
 * @param string $name
 * @return string
 */
function echo_select_soderzit($name) {
    $r = "<label for = \"$name\">Вхождение</label>";
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

/**
 * @param string $name
 * @return string
 */
function echo_select_fuzziness($name) {
    $r = "<label for = \"$name\">Вхождение</label>";
    $r .= "<select id = \"$name\" name='$name' class=\"form-control\">";

    $values = [
        1 => 'точная фраза',
        2 => 'искать с опечатками',
        3 => 'начинается с',
        4 => 'заканчивается на'
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

$cemeteries = Cemetery::find()->orderBy('name')->all();
$cemeteriesFormated = ArrayHelper::toArray($cemeteries, [
    'app\models\Cemetery' => [
        'id',
        'name',
    ],
]);

?>
    <h2>Поиск по захоронениям г. Санкт-Петербурга</h2>
    <hr />
    <h4>Основные параметры</h4>
    <div class="search-form" id="filter-container">
        <div class="container">
            <div class="row">
                <div class="col-sm-2">
                    <label for='fam'>Фамилия</label>
                    <input value="<?= get_input_value("fam") ?>" type="text" class='form-control' name='fam' id='fam' />
                </div>
                <div class="col-sm-1">
                    <?= echo_select_fuzziness('fam_cont') ?>
                </div>
                <div class="col-sm-2">
                    <label for='nam'>Имя</label>
                    <input value="<?= get_input_value("nam") ?>" type="text" class='form-control' name='nam' id='nam' />
                </div>
                <div class="col-sm-1">
                    <?= echo_select_fuzziness('nam_cont') ?>
                </div>
                <div class="col-sm-2">
                    <label for='ot'>Отчество</label>
                    <input value="<?= get_input_value("ot") ?>" type="text" class='form-control' name='ot' id='ot' />
                </div>
                <div class="col-sm-1">
                    <?= echo_select_fuzziness('ot_cont') ?>
                </div>
                <div class="col-sm-2">
                    <label for='regnum'>Номер записи</label>
                    <input value="<?= get_input_value("regnum") ?>" type="text" class='form-control' name='regnum' id='regnum' />
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
                    <a><input type='checkbox' id='ext_search' name='ext_search'>Дополнительные параметры</a>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-1">
                    <label for="age">Возраст</label>
                    <input value="<?= get_input_value("age") ?>"  type=text class='form-control' id='age' name='age' />
                </div>
                <div class="col-sm-1">
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

                <div class="col-sm-3">
                    <label for="dead_year">Дата смерти</label>
                    <div class="input-group mb-2">
                        <input value="<?= get_input_value("dead_y") ?>" id="dead_y" name="dead_y" type="text" class="form-control" placeholder="Год" >
                        <span class="input-group-text">.</span>
                        <input value="<?= get_input_value("dead_m") ?>" id="dead_m" name="dead_m" type="text" class="form-control" placeholder="Месяц" >
                        <span class="input-group-text">.</span>
                        <input value="<?= get_input_value("dead_d") ?>" id="dead_d" name="dead_d" type="text" class="form-control" placeholder="День" >
                    </div>
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

                $selected2 = ['selected', '', ''];
                if (isset($_GET['rip_year_cmp'])) {
                    switch (intval($_GET['rip_year_cmp'])) {
                        case 2:
                            $selected2 = ['', 'selected', ''];
                            BREAK;
                        case 3:
                            $selected2 = ['', '', 'selected'];
                            BREAK;
                    }
                }
                ?>

                <div class="col-sm-1">
                    <label for="dead_year_cmp">Сравнение</label>
                    <select id="dead_year_cmp" name='dead_year_cmp' class="form-control">
                        <option  <?= $selected[0] ?> value='1'>Равно</option>
                        <option  <?= $selected[1] ?> value='2'>Меньше</option>
                        <option  <?= $selected[2] ?> value='3'>Больше</option>
                    </select>
                </div>

                <div class="col-sm-3">
                    <label for="dead_year">Дата захоронения</label>
                    <div class="input-group mb-2">
                        <input value="<?= get_input_value("rip_y") ?>" id="rip_y" name="rip_y" type="text" class="form-control" placeholder="Год" >
                        <span class="input-group-text">.</span>
                        <input value="<?= get_input_value("rip_m") ?>" id="rip_m" name="rip_m" type="text" class="form-control" placeholder="Месяц" >
                        <span class="input-group-text">.</span>
                        <input value="<?= get_input_value("rip_d") ?>" id="rip_d" name="rip_d" type="text" class="form-control" placeholder="День" >
                    </div>
                </div>  

                <div class="col-sm-1">
                    <label for="rip_year_cmp">Сравнение</label>
                    <select id="rip_year_cmp" name='rip_year_cmp' class="form-control">
                        <option <?= $selected2[0] ?> value='1'>Равно</option>
                        <option <?= $selected2[1] ?> value='2'>Меньше</option>
                        <option <?= $selected2[2] ?> value='3'>Больше</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label for="zags">ЗАГС</label>
                        <input name="zags" id="zags" class="form-control" value="<?= get_input_value("zags") ?>">
                    </div>
                </div>
                <div class="col-sm-1">
                    <?= echo_select_fuzziness('zags_cont') ?>
                </div>
                <div class="col-sm-2">
                    <div class="form-group">
                        <label for="docnum">Номер документа</label>
                        <input value="<?= get_input_value("docnum") ?>" type=text class='form-control' id='docnum' name='docnum' />
                    </div>
                </div>
                <div class="col-sm-5">
                    <div class="form-group">
                        <label for="comment">Комментарий</label>
                        <input value="<?= get_input_value("comment") ?>" type=text class='form-control' id='comment' name='comment' />
                    </div>
                </div>           
                <div class="col-sm-2">
                    <button type="submit" id="find_results" class="btn btn-primary btn-lg btn-block search_btn">Найти</button>            
                </div>
            </div>

            <hr />

            <div id='additional_search_params' class='d-none'>
                <h4>Дополнительные параметры</h4>
                <div class="row">
                    <div class="col-sm-2">
                        <label for="areanum">Номер участка</label>
                        <input value="<?= get_input_value("areanum") ?>" type=text class='form-control' id='areanum' name='areanum' />
                    </div>
                    <div class="col-sm-2">
                        <?= echo_select_fuzziness('area_cont') ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="rownum">Номер ряда</label>
                        <input value="<?= get_input_value("rownum") ?>" type=text class='form-control' id='rownum' name='rownum' />
                    </div>
                    <div class="col-sm-2">
                        <?= echo_select_fuzziness('row_cont') ?>
                    </div>
                    <div class="col-sm-2">
                        <label for="ripnum">Номер могилы</label>
                        <input value="<?= get_input_value("ripnum") ?>" type=text class='form-control' id='ripnum' name='ripnum' />
                    </div>
                    <div class="col-sm-2">
                        <?= echo_select_fuzziness('rip_cont') ?>
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

            <div class="row d-none" id="search_results">
                <div class="col-sm-12">
                    <ul id="tabs">
                    </ul>
                </div>
            </div>

            <?php
                /*if ($search_data)
                    echo Yii::$app->controller->renderPartial('_search_result', ['data' => $search_data]);
                else if (isset($_GET['fam'])) {
                    $url_f2 = '/web/print/not-found-f2';
                    $url_f2 .= "?params=" . rawurlencode(base64_encode(serialize($_GET)));
                    echo "<h5>По вашему запросу ничего не найдено, попробуйте уточнить критерии поиска <a target='_blank' href='$url_f2'>Форма Ф2</a></h5>";
                }*/
            ?>
        </div>
    </div>
<script>
    document.addEventListener('DOMContentLoaded', () => { 
        // 1. Универсальная функция инициализации Autocomplete
        const initAutocomplete = (selector, variableName) => {
            $(selector).autocomplete({
                source: (request, response) => {
                    $.ajax({
                        url: "<?= \yii\helpers\Url::to(['search/search-suggest']) ?>",
                        dataType: "json",
                        data: {
                            q: request.term,
                            variable: variableName
                        },
                        success: data => response(data)
                    });
                },
                minLength: 2
            });
        };

        // Массив полей для подключения автодополнения
        const autocompleteFields = [
            { selector: '#regnum', name: 'regnum' },
            { selector: '#fam', name: 'fam' },
            { selector: '#nam', name: 'nam' },
            { selector: '#ot', name: 'ot' },
            { selector: '#zags', name: 'zags' },
            { selector: '#comment', name: 'comment' },
            { selector: '#rel', name: 'relative' },
            { selector: '#unknown_number', name: 'unknown_number' },
            { selector: '#docnum', name: 'docnum' },
            { selector: '#areanum', name: 'areanum' },
            { selector: '#rownum', name: 'rownum' },
            { selector: '#ripnum', name: 'ripnum' }
        ];

        autocompleteFields.forEach(({ selector, name }) => initAutocomplete(selector, name));

        // 2. Переключение расширенного поиска
        document.getElementById('ext_search').addEventListener('click', (event) => {
            const $additionalParams = $('#additional_search_params');
            const $extSearchCheckbox = $('#ext_search');
            
            const isVisible = $additionalParams.is(':visible') && !$additionalParams.hasClass('d-none');

            if (isVisible) {
                $additionalParams.addClass('d-none');
                $extSearchCheckbox.prop('checked', false);
            } else {
                if (!validateForm(true)){
                    $extSearchCheckbox.prop('checked', false);
                    return;
                }

                $extSearchCheckbox.prop('checked', true);
                $additionalParams.removeClass('d-none');
            }
        });

        // Глобальная переменная для хранения текущих фильтров поиска
        let currentFilterObject = {};

        // 3. Выполнение поиска и построение вкладок
        document.getElementById('find_results').addEventListener('click', (event) => {
            if (!validateForm(true)) return;

            currentFilterObject = {};

            document.querySelectorAll('#filter-container input, #filter-container select').forEach(input => {
                if (!input.name) return;

                if (input.type === 'checkbox') {
                    currentFilterObject[input.name] = input.checked ? 1 : 0;
                } else {
                    currentFilterObject[input.name] = input.value;
                }
            });

            $.ajax({
                url: '<?= \yii\helpers\Url::to(['search/found-result-exists']) ?>',
                type: 'GET',
                data: { search: currentFilterObject },
                success: function(response) {
                    const $tabsContainer = $('#tabs');
                    
                    // Уничтожаем старый экземпляр jQuery UI Tabs
                    if ($tabsContainer.data('ui-tabs')) {
                        $tabsContainer.tabs('destroy');
                    }

                    $tabsContainer.empty(); // Очищаем контейнер

                    const activeItems = response.filter(item => item.exists);
                    if (activeItems.length === 0) {
                        alert('Ничего не найдено');
                        return;
                    }

                    // Создаем базовую структуру <ul>
                    const $ul = $('<ul></ul>');

                    // Заполняем <ul> ссылками на табы и сразу генерируем контейнеры под них
                    activeItems.forEach(item => {
                        $ul.append(`<li><a href="#tabs-${item.id}" data-id="${item.id}">${item.name}</a></li>`);
                        
                        $tabsContainer.append(`
                            <div id="tabs-${item.id}" data-id="${item.id}" data-loaded="false">
                                <div class="p-3">Загрузка данных...</div>
                            </div>
                        `);
                    });

                    $tabsContainer.prepend($ul);

                    // Инициализируем jQuery UI Tabs с обработчиком переключения (beforeActivate)
                    $tabsContainer.tabs({
                        beforeActivate: function(event, ui) {
                            const $newTabPanel = ui.newPanel;
                            loadTabContent($newTabPanel);
                        }
                    });

                    // Показываем блок результатов
                    document.querySelector('#search_results').classList.remove('d-none');

                    // Ручной вызов загрузки первой активной вкладки
                    const $firstPanel = $tabsContainer.find('.ui-tabs-panel').first();
                    if ($firstPanel.length) {
                        loadTabContent($firstPanel);
                    }
                }
            });
        });

        // 4. Функция ленивой загрузки содержимого вкладки
        function loadTabContent($tabPanel) {
            const isLoaded = $tabPanel.attr('data-loaded') === 'true';
            const itemId = $tabPanel.attr('data-id');

            currentFilterObject.cemetery = itemId;

            if (!isLoaded) {
                $.ajax({
                    url: '<?= \yii\helpers\Url::to(['search/show-search-result']) ?>',
                    type: 'GET',
                    data: {
                        page: 1,
                        search: currentFilterObject // Передаем условия поиска в контроллер
                    },
                    success: function(html) {
                        $tabPanel.html(html);
                        $tabPanel.attr('data-loaded', 'true');
                    },
                    error: function() {
                        $tabPanel.html('<div class="p-3 text-danger">Ошибка при загрузке данных.</div>');
                    }
                });
            }
        }

        // 5. Валидация формы
        function validateForm(showAlert = false) {
            const fields = [
                '#nam', '#fam', '#ot', '#age', 
                '#dead_y', '#dead_m', '#dead_d', 
                '#rip_y', '#rip_m', '#rip_d', 
                '#regnum', '#zags', '#docnum', '#comment'
            ];

            const hasValue = fields.some(selector => Boolean($(selector).val()?.trim()));
            const hasCemetery = $('#cemetery').val() !== '0';
            const isUnknownChecked = $('#unknown').is(':checked');

            const isValid = hasValue || hasCemetery || isUnknownChecked;

            if (!isValid && showAlert) {
                alert("Требуются данные основного поиска");
            }

            return isValid;
        }
    });

    /*function vopros(record_id) {
        jQuery.get("/web/search/vopros", {"record_id": record_id}, function (data) {
            alert("Данные отправлены на уточнение");
            jQuery('#vopros' + record_id).fadeOut(400);
        });
    }*/

    /*function next_page(cemetery_id, page) {
        var val = jQuery('#pager').val();

        if (val)
            val += ";";

        val += cemetery_id + "," + page;
        jQuery('#pager').val(val);
        jQuery('.search_btn').click();
    }*/

</script>
