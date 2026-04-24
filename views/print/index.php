<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Record;
use app\models\Cemetery;
use app\models\Helper;

$model = $record;

$this->title = "Печать: " . $cemetery->name . ", " . $record->fio;
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$page_num = 1;
$fname = strtr($record->filename, ["\\" => '/']);
if (preg_match("#.*?/([^/]*?)\.jp.*?$#", $fname, $pmatch)) {
    //echo $record->filename;
    //print_r($pmatch);
    $page_num = $pmatch[1];
    //exit;
}

$grob = Record::ripStyleTypes()[$record->rip_style];
if ($model->book->rip_style) {
    $grob = Book::ripStyleTypes()[$model->book->rip_style];
    //echo $grob;exit;
}


$zah_suffix = ', уч. ' . $record->area_num . ", ряд " . $record->row_num . ", место " . $record->rip_num;

$zahoronen_base = $cemetery->name . "__GROB__" . $zah_suffix;

$zahoronen = $cemetery->name . ', ' . $grob . $zah_suffix;

$user_fio = $user->lastname . ' ' . $user->firstname . ' ' . $user->middlename;
if ($user->middlename) {
    $user_fio = $user->lastname . ' ' . mb_substr($user->firstname, 0, 1, 'utf8') . '. ' . mb_substr($user->middlename, 0, 1, 'utf8') . '.';
}

$otv_lico = '';

$zahoronen = strtr($zahoronen, ['"' => "&quot;"]);
?>
<div class="print-view">

    <h5><?= Html::encode($this->title) ?></h5>
    <form method="get" action="/web/print/forma-pdf">
        <div class="container">

            <div class="row">
                <div class="col-sm-6">
                    <label for="nn">Номер</label>
                    <input class="form-control" type="text" name="nn" id="nn" value="" />
                </div>
                <div class="col-sm-6">
                    <label for="date">Дата выдачи</label>
                    <input class="form-control" type="text" name="date" id="date" value="<?= date('d.m.Y') ?>" />
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6">
                    <label for="vidano">Справка выдана (ФИО)</label>
                    <input class="form-control" type="text" name="vidano" id="vidano" value="" />
                </div>
                <div class="col-sm-6">
                    <label for="fio">ФИО умершего</label>
                    <input class="form-control" type="text" name="fio" id="fio" value="<?= $record->fio ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label for="docnum">Номер документа</label>
                    <input class="form-control" type="text" name="docnum" id="docnum" value="<?= $record->docnum ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="rip_date">Дата захоронения</label>
                    <input class="form-control" type="text" name="rip_date" id="rip_date" value="<?= Helper::formatDate($record->rip_date) ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="death_date">Дата смерти</label>
                    <input class="form-control" type="text" name="death_date" id="death_date" value="<?= Helper::formatDate($record->death_date) ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="age">Возраст</label>
                    <input class="form-control" type="text" name="age" id="age" value="<?= $record->age ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label for="svazka">Номер связки</label>
                    <input class="form-control" type="text" name="svazka" id="svazka" value="<?= $book->svazka ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="book_num">Номер книги</label>
                    <input class="form-control" type="text" name="book_num" id="book_num" value="<?= $book->number ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="page_num">Страница</label>
                    <input class="form-control" type="text" name="page_num" id="page_num" value="<?= $sdata->page_num ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="pp">п/п</label>
                    <input class="form-control" type="text" name="pp" id="pp" value="<?= $sdata['regnum'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3"> 
                    <label for="zahr">Кладбище</label>
                    <input class="form-control" type="text" name="cemetery" id="cemetery" value="<?= $cemetery->name ?>" />
                </div>
                <div class="col-sm-9"> 
                    <label for="zahr">Захоронен(а)</label>
                    <input class="form-control" type="text" name="zahr" id="zahr" value="<?= $zahoronen ?>" />

                </div>
            </div>

            <div class="row">
                <div class="col-sm-4"> 
                    <label for="author">Специалист по работе с архивом</label>
                    <input class="form-control" type="text" name="author" id="author" value="<?= $user_fio ?>" />

                </div>
                <div class="col-sm-4"> 
                    <label for="author2">Ответственное лицо</label>
                    <input class="form-control" type="text" name="author2" id="author2" value="<?= $record->relative_fio ?>" />
                </div>
                <div class="col-sm-4"> 
                    <label for="author2">ЗАГС</label>
                    <input class="form-control" type="text" name="zags" id="zags" value="<?= $record->zags ?>" />
                </div>
            </div>



            <div class="row">
                <div class="col-sm-12"> 
                    <label for="comment">Комментарий</label>
                    <input class="form-control" type="text" name="comment" id="comment" value="" />

                </div>

            </div>

            <div class="row">
                <div class="col-sm-12"> 
                    <div class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="checkbox" name="print_date" id="print_date" value="1">
                        <label class="form-check-label" for="print_date">печатать дату смерти</label>
                    </div>
                    <div  class="form-check form-check-inline">
                        <input onchange="click_chechbox_zahr();" checked="checked" class="form-check-input" type="checkbox" name="print_grob" id="print_grob" value="2">
                        <label class="form-check-label" for="print_grob">Печатать способ захоронения (гроб/урна)</label>
                    </div>
                    <div  class="form-check form-check-inline">
                        <input onchange="click_addr_otv();" checked="checked" class="form-check-input" type="checkbox" name="print_addr" id="print_addr" value="2">
                        <label class="form-check-label" for="print_addr">Печатать адрес ответственного лица</label>
                    </div>

                    <div  class="form-check form-check-inline">
                        <input  checked="checked" class="form-check-input" type="checkbox" name="print_comment" id="print_comment" value="2">
                        <label class="form-check-label" for="print_comment">Печатать комментарий</label>
                    </div>

                </div>
            </div>

            <div class="row">
                <div class="col-sm-6"> 
                    <div class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="radio" name="spravka" id="inlineRadio1" value="1">
                        <label class="form-check-label" for="inlineRadio1">СПРАВКА (Ф-1)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="spravka" id="inlineRadio2" value="2">
                        <label class="form-check-label" for="inlineRadio2">СПРАВКА (Ф-2)</label>
                    </div>
                </div>

            </div>


            <div class="row">
                <div class="col-sm-12"> 
                    <div class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="radio" name="saveas" id="inlinRadio1" value="1">
                        <label class="form-check-label" for="inlinRadio1">pdf</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="saveas" id="inlinRadio2" value="2">
                        <label class="form-check-label" for="inlinRadio2">jpg</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="radio" name="saveas" id="inlinRadio3" value="3">
                        <label class="form-check-label" for="inlinRadio3">Сохранить в pdf</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="saveas" id="inlinRadio4" value="4">
                        <label class="form-check-label" for="inlinRadio4">Сохранить в jpg</label>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-sm-6"> 
                    <input type="submit" value="печать" class="btn btn-primary btn-lg btn-block ">
                    <input type="hidden" name="record_id" value="<?= $record->id ?>">
                </div>
            </div>

        </div>
    </form>
</div>

<script>

    var zahr = "<?= $zahoronen_base ?>";
    var grob = "<?= $grob ?>";
    function click_chechbox_zahr() {
        var chk = jQuery("#print_grob").is(":checked");
        if (chk) {
            jQuery("#zahr").val(zahr.replace("__GROB__", ", " + grob));
        } else {
            jQuery("#zahr").val(zahr.replace("__GROB__", ""));

        }
    }

    var author2 = '<?= $record->relative_fio ?>';
    var author2_short = '<?php
//$record->relative_fio = strtr($record->relative_fio, [',' => '.']);
$pos = strpos($record->relative_fio, ',');
if ($pos > 0) {
    echo substr($record->relative_fio, 0, $pos);
} else
    echo $record->relative_fio;
?>';



    function click_addr_otv() {
        var chk = jQuery("#print_addr").is(":checked");
        if (chk) {
            jQuery("#author2").val(author2);
        } else {
            jQuery("#author2").val(author2_short);

        }
    }

</script>