<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Record;
use app\models\Cemetery;

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

$zahoronen = $cemetery->name . ', ' . $record->area_num . ", " . $record->row_num . ", " . $record->rip_num;

$user_fio = $user->lastname . ' ' . $user->firstname . ' ' . $user->middlename;
?>
<div class="print-view">

    <h5><?= Html::encode($this->title) ?></h5>
    <form method="get" action="/web/print/forma">
        <div class="container">

            <div class="row">
                <div class="col-sm-6">
                    <label for="nn">Номер</label>
                    <input class="form-control" type="text" name="nn" id="nn" value="" />
                </div>
                <div class="col-sm-6">
                    <label for="date">Дата выдачи</label>
                    <input class="form-control" type="text" name="date" id="date" value="<?= date('Y-m-d') ?>" />
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
                <div class="col-sm-6">
                    <label for="docnum">Номер документа</label>
                    <input class="form-control" type="text" name="docnum" id="docnum" value="<?= $record->docnum ?>" />
                </div>
                <div class="col-sm-6">
                    <label for="rip_date">Дата захоронения</label>
                    <input class="form-control" type="text" name="rip_date" id="rip_date" value="<?= $record->rip_date ?>" />
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
                <div class="col-sm-6"> 
                    <input type="submit" value="печать" class="btn btn-primary btn-lg btn-block ">
                </div>
            </div>

        </div>
    </form>
</div>