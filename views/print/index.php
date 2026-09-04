<?php

use yii\helpers\Html;
use app\models\Book;
use app\models\Record;
use app\models\Helper;

/**
 * @var \app\models\Record $record
 * @var \app\models\CacheRecords $sdata
 * @var yii\web\View $this
 * @var \app\models\User $user
 */

$book = $record->book ?? null;
$cemetery = $book->cemetery ?? null;

$this->title = "Печать" . ($record ? ": {$cemetery->name}, {$record->fio}" : '');
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

// Определение способа захоронения (приоритет у книги)
$grob = '';
if (!empty($book->rip_style)) {
    $grob = Book::ripStyleTypes()[$book->rip_style] ?? '';
} elseif ($record) {
    $grob = Record::ripStyleTypes()[$record->rip_style] ?? '';
}

// Формирование базового шаблона места захоронения
$zah_suffix = $record ? ", уч. {$record->area_num}, ряд {$record->row_num}, место {$record->rip_num}" : '';
$zahoronen_base = ($cemetery->name ?? '') . "__GROB__" . $zah_suffix;

// Формирование ФИО оператора
$user_fio = $user->middlename 
    ? $user->lastname . ' ' . mb_substr($user->firstname, 0, 1, 'utf8') . '. ' . mb_substr($user->middlename, 0, 1, 'utf8') . '.'
    : "$user->lastname $user->firstname $user->middlename";

// Собираем данные записи
$res = [
    'fio'          => $record->fio ?? '',
    'docnum'       => $record->docnum ?? '',
    'age'          => $record->age ?? '',
    'relative_fio' => $record->relative_fio ?? '',
    'zags'         => $record->zags ?? '',
    'comment'      => $record->comment ?? '',
    'number'       => $book->number ?? '',
    'svazka'       => $book->svazka ?? '',
    'page_num'     => $sdata->page_num ?? '',
    'regnum'       => $sdata->regnum ?? '',
    'rip_date'     => Helper::formatDate($record->rip_date ?? ''),
    'death_date'   => Helper::formatDate($record->death_date ?? ''),
    'cemetery'     => $cemetery->name ?? '',
];
?>

<div class="print-view">
    <h5><?= Html::encode($this->title) ?></h5>
    <form method="get" action="/web/print/forma">
        <div class="container">
            
            <div class="row">
                <div class="col-sm-6">
                    <label for="nn">Номер</label>
                    <input class="form-control" type="text" name="nn" id="nn" />
                </div>
                <div class="col-sm-6">
                    <label for="date">Дата выдачи</label>
                    <input class="form-control" type="text" name="date" id="date" value="<?= date('d.m.Y') ?>" />
                </div>
                <div class="col-sm-6">
                    <label for="vidano">Справка выдана (ФИО)</label>
                    <input class="form-control" type="text" name="vidano" id="vidano" />
                </div>
                <div class="col-sm-6">
                    <label for="fio">ФИО умершего</label>
                    <input class="form-control" type="text" name="fio" id="fio" value="<?= $res['fio'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label for="docnum">Номер документа</label>
                    <input class="form-control" type="text" name="docnum" id="docnum" value="<?= $res['docnum'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="rip_date">Дата захоронения</label>
                    <input class="form-control" type="text" name="rip_date" id="rip_date" value="<?= $res['rip_date'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="death_date">Дата смерти</label>
                    <input class="form-control" type="text" name="death_date" id="death_date" value="<?= $res['death_date'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="age">Возраст</label>
                    <input class="form-control" type="text" name="age" id="age" value="<?= $res['age'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3">
                    <label for="svazka">Номер связки</label>
                    <input class="form-control" type="text" name="svazka" id="svazka" value="<?= $res['svazka'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="book_num">Номер книги</label>
                    <input class="form-control" type="text" name="book_num" id="book_num" value="<?= $res['number'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="page_num">Страница</label>
                    <input class="form-control" type="text" name="page_num" id="page_num" value="<?= $res['page_num'] ?>" />
                </div>
                <div class="col-sm-3">
                    <label for="pp">п/п</label>
                    <input class="form-control" type="text" name="pp" id="pp" value="<?= $res['regnum'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3"> 
                    <label for="cemetery">Кладбище</label>
                    <input class="form-control" type="text" name="cemetery" id="cemetery" value="<?= $res['cemetery'] ?>" />
                </div>
                <div class="col-sm-9"> 
                    <label for="zahr">Захоронен(а)</label>
                    <input class="form-control" type="text" name="zahr" id="zahr" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-4"> 
                    <label for="author">Специалист по работе с архивом</label>
                    <input class="form-control" type="text" name="author" id="author" value="<?= $user_fio ?>" />
                </div>
                <div class="col-sm-4"> 
                    <label for="author2">Ответственное лицо</label>
                    <input class="form-control" type="text" name="author2" id="author2" value="<?= $res['relative_fio'] ?>" />
                </div>
                <div class="col-sm-4"> 
                    <label for="zags">ЗАГС</label>
                    <input class="form-control" type="text" name="zags" id="zags" value="<?= $res['zags'] ?>" />
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12"> 
                    <label for="comment">Комментарий</label>
                    <input class="form-control" type="text" name="comment" id="comment" value="<?= $res['comment'] ?>"/>
                </div>
            </div>

            <!-- Чекбоксы опций -->
            <div class="row">
                <div class="col-sm-12"> 
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="checkbox" name="print_date" id="print_date" value="1">
                        <label class="form-check-label" for="print_date">печатать дату смерти</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="checkbox" name="print_grob" id="print_grob" value="2">
                        <label class="form-check-label" for="print_grob">Печатать способ захоронения (гроб/урна)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="checkbox" name="print_addr" id="print_addr" value="2">
                        <label class="form-check-label" for="print_addr">Печатать адрес ответственного лица</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="checkbox" name="print_comment" id="print_comment" value="2">
                        <label class="form-check-label" for="print_comment">Печатать комментарий</label>
                    </div>
                </div>
            </div>

            <!-- Переключатели типа справки -->
            <div class="row">
                <div class="col-sm-6"> 
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="radio" name="spravka" id="inlineRadio1" value="1">
                        <label class="form-check-label" for="inlineRadio1">СПРАВКА (Ф-1)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="spravka" id="inlineRadio2" value="2">
                        <label class="form-check-label" for="inlineRadio2">СПРАВКА (Ф-2)</label>
                    </div>
                </div>
            </div>

            <!-- Форматы сохранения -->
            <div class="row">
                <div class="col-sm-12"> 
                    <div class="form-check form-check-inline">
                        <input checked class="form-check-input" type="radio" name="saveas" id="inlinRadio1" value="1">
                        <label class="form-check-label" for="inlinRadio1">pdf</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="saveas" id="inlinRadio2" value="2">
                        <label class="form-check-label" for="inlinRadio2">jpg</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="saveas" id="inlinRadio3" value="3">
                        <label class="form-check-label" for="inlinRadio3">Сохранить в pdf</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="saveas" id="inlinRadio4" value="4">
                        <label class="form-check-label" for="inlinRadio4">Сохранить в jpg</label>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-sm-6"> 
                    <input type="submit" value="печать" class="btn btn-primary btn-lg btn-block">
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const zahrBase = <?= json_encode($zahoronen_base) ?>;
        const grob = <?= json_encode($grob) ?>;
        const relativeFio = <?= json_encode($res['relative_fio']) ?>;

        const printGrob = document.getElementById('print_grob');
        const printAddr = document.getElementById('print_addr');
        const zahrInput = document.getElementById('zahr');
        const author2Input = document.getElementById('author2');

        // Обновление поля "Захоронен(а)"
        const updateZahr = () => {
            const replacement = (printGrob.checked && grob.trim()) ? `, ${grob}` : '';
            zahrInput.value = zahrBase.replace('__GROB__', replacement);
        };

        // Обновление поля "Ответственное лицо"
        const updateAuthor = () => {
            author2Input.value = printAddr.checked 
                ? relativeFio 
                : relativeFio.split(',')[0];
        };

        printGrob.addEventListener('change', updateZahr);
        printAddr.addEventListener('change', updateAuthor);

        // Первоначальная инициализация значений
        updateZahr();
        updateAuthor();
    });
</script>