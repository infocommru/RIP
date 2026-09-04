<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Record;
use app\models\Cemetery;
use app\models\Helper;

/**
 * @var \app\models\Record $record
 * @var \app\models\Cemetery $cemetery
 * @var \app\models\Book $book
 * @var \app\models\CacheRecords $sdata
 * @var yii\web\View $this
 * @var \app\models\User $user
 */

$cemetery = $record->book->cemetery ?? null;

$this->title = "Печать" . (isset($record) ? (": $cemetery->name, $record->fio") : '');
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$grob = isset($record) ? Record::ripStyleTypes()[$record->rip_style] : '';

if (!empty($record->book->rip_style))
    $grob = Book::ripStyleTypes()[$record->book->rip_style ?? 0];

$zah_suffix = '';

if(isset($record)){
    $zah_suffix = ', уч. ' . ($record->area_num ?? '') . 
            ", ряд " . ($record->row_num ?? '') . ", место " . ($record->rip_num ?? '');
}


$zahoronen_base = ($cemetery->name ?? '') . "__GROB__" . $zah_suffix;
$zahoronen = ($cemetery->name ?? '') . (isset($record) ? (', ' . $grob . $zah_suffix) : '');

$user_fio = "$user->lastname $user->firstname $user->middlename";

if ($user->middlename) {
    $user_fio = $user->lastname . ' ' . mb_substr($user->firstname, 0, 1, 'utf8') . '. ' . mb_substr($user->middlename, 0, 1, 'utf8') . '.';
}

$zahoronen = strtr($zahoronen, ['"' => "&quot;"]);

$results = [];
$results['fio'] = $record->fio ?? '';
$results['docnum'] = $record->docnum ?? '';
$results['age'] = $record->age ?? '';
$results['relative_fio'] = $record->relative_fio ?? '';
$results['zags'] = $record->zags ?? '';
$results['comment'] = $record->comment ?? '';

$results['number'] = $record->book->number ?? '';
$results['svazka'] = $record->book->svazka ?? '';

$results['page_num'] = $sdata->page_num ?? '';
$results['regnum'] = $sdata->regnum ?? '';

$results['rip_date'] = Helper::formatDate($record->rip_date ?? '');
$results['death_date'] = Helper::formatDate($record->death_date ?? '');

$results['cemetery_name'] = $cemetery->name ?? '';
?>

<div class="print-view">
    <h5><?= Html::encode($this->title) ?></h5>
    <form method="get" action="/web/print/forma">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <label for="nn">Номер</label>
                    <input class="form-control" type="text" name="nn" id="nn"/>
                </div>
                <div class="col-sm-6">
                    <label for="date">Дата выдачи</label>
                    <input class="form-control" type="text" name="date" id="date" value="<?= date('d.m.Y') ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <label for="vidano">Справка выдана (ФИО)</label>
                    <input class="form-control" type="text" name="vidano" id="vidano"/>
                </div>
                <div class="col-sm-6">
                    <label for="fio">ФИО умершего</label>
                    <input class="form-control" type="text" name="fio" id="fio" value="<?= $results['fio'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label for="docnum">Номер документа</label>
                    <input class="form-control" type="text" name="docnum" id="docnum" value="<?= $results['docnum'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="rip_date">Дата захоронения</label>
                    <input class="form-control" type="text" name="rip_date" id="rip_date" value="<?= $results['rip_date'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="death_date">Дата смерти</label>
                    <input class="form-control" type="text" name="death_date" id="death_date" value="<?= $results['death_date'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="age">Возраст</label>
                    <input class="form-control" type="text" name="age" id="age" value="<?= $results['age'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label for="svazka">Номер связки</label>
                    <input class="form-control" type="text" name="svazka" id="svazka" value="<?= $results['svazka'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="book_num">Номер книги</label>
                    <input class="form-control" type="text" name="book_num" id="book_num" value="<?= $results['number'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="page_num">Страница</label>
                    <input class="form-control" type="text" name="page_num" id="page_num" value="<?= $results['page_num'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="pp">п/п</label>
                    <input class="form-control" type="text" name="pp" id="pp" value="<?= $results['regnum'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3"> 
                    <label for="zahr">Кладбище</label>
                    <input class="form-control" type="text" name="cemetery" id="cemetery" value="<?= $results['cemetery_name'] ?>" />
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
                    <input class="form-control" type="text" name="author2" id="author2" value="<?= $results['relative_fio'] ?>" />
                </div>
                <div class="col-sm-4"> 
                    <label for="author2">ЗАГС</label>
                    <input class="form-control" type="text" name="zags" id="zags" value="<?= $results['zags'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12"> 
                    <label for="comment">Комментарий</label>
                    <input class="form-control" type="text" name="comment" id="comment" value="<?= $results['comment'] ?>"/>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12"> 
                    <div class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="checkbox" name="print_date" id="print_date" value="1">
                        <label class="form-check-label" for="print_date">печатать дату смерти</label>
                    </div>
                    <div  class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="checkbox" name="print_grob" id="print_grob" value="2">
                        <label class="form-check-label" for="print_grob">Печатать способ захоронения (гроб/урна)</label>
                    </div>
                    <div  class="form-check form-check-inline">
                        <input checked="checked" class="form-check-input" type="checkbox" name="print_addr" id="print_addr" value="2">
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
                    <input type="submit" value="печать" class="btn btn-primary btn-lg btn-block">
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        let zahr = "<?= $zahoronen_base ?>";
        let grob = "<?= $grob ?>";

        let author2 = '<?= $record->relative_fio ?? '' ?>';
        let author2_short = author2.split(',')[0];

        document.getElementById('print_grob').addEventListener('click', (event) => {
            let chk = jQuery("#print_grob").is(":checked");

            if (chk && grob.trim()) {
                jQuery("#zahr").val(zahr.replace("__GROB__", ", " + grob));
            } else {
                jQuery("#zahr").val(zahr.replace("__GROB__", ""));
            }
        });

        document.getElementById('print_addr').addEventListener('click', (event) => {
            let chk = jQuery("#print_addr").is(":checked");

            if (chk) {
                jQuery("#author2").val(author2);
            } else {
                jQuery("#author2").val(author2_short);
            }
        });
    });
</script>