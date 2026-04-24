<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Record;
use app\models\Cemetery;
use app\models\Helper;

$this->title = "Форма Ф2";
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$user_fio = $user->lastname . ' ' . $user->firstname . ' ' . $user->middlename;
if ($user->middlename) {
    $user_fio = $user->lastname . ' ' . mb_substr($user->firstname, 0, 1, 'utf8') . '. ' . mb_substr($user->middlename, 0, 1, 'utf8') . '.';
}

$otv_lico = '';

//print_r($params);
//exit;

$fio = '';
if ($params)
    $fio = $params['fam'] . ' ' . $params['nam'] . ' ' . $params['ot'];
$fio = trim($fio);

$cemetery_name = "";
if ($params) {
    $cemetery_id = intval($params['cemetery']);
    if ($cemetery_id) {
        $cemetery = Cemetery::find()->andWhere(['id' => $cemetery_id])->one();
        $cemetery_name = $cemetery->name;
    }
}

//print_r($params);
//exit;

$rip_year = '';
if ($params)
    $rip_year = $params['rip_y'];
?>
<div class="print-view">

    <h5><?= Html::encode($this->title) ?></h5>
    <form method="get" action="/web/print/forma-pdf">
        <div class="container">

            <div class="row">
                <div class="col-sm-4">
                    <label for="nn">Номер</label>
                    <input class="form-control" type="text" name="nn" id="nn" value="" />
                </div>
                <div class="col-sm-4">
                    <label for="date">Дата выдачи</label>
                    <input class="form-control" type="text" name="date" id="date" value="<?= date('d.m.Y') ?>" />
                </div>
                <div class="col-sm-4">
                    <label for="dead_year">Год захоронения</label>
                    <input class="form-control" type="text" name="dead_year" id="dead_year" value="<?= $rip_year ?>" />
                    <input type="hidden" name="spravka" value="2">
                </div>
            </div>


            <div class="row">
                <div class="col-sm-6">
                    <label for="vidano">Справка выдана (ФИО)</label>
                    <input class="form-control" type="text" name="vidano" id="vidano" value="" />
                </div>
                <div class="col-sm-6">
                    <label for="fio">ФИО умершего</label>
                    <input class="form-control" type="text" name="fio" id="fio" value="<?= $fio ?>" />
                </div>
            </div>


            <div class="row">
                <div class="col-sm-3"> 
                    <label for="zahr">Кладбище</label>
                    <input class="form-control" type="text" name="cemetery" id="cemetery" value="<?= $cemetery_name ?>" />
                </div>
                <div class="col-sm-9"> 
                    <label for="author">Специалист по работе с архивом</label>
                    <input class="form-control" type="text" name="author" id="author" value="<?= $user_fio ?>" />

                </div>
            </div>

            <div class="row">
                <div class="col-sm-12"> 

                    <div  class="form-check form-check-inline">
                        <input  checked="checked" class="form-check-input" type="checkbox" name="print_comment" id="print_comment" value="2">
                        <label class="form-check-label" for="print_comment">Печатать ИПС комментарий</label>
                    </div>

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
                </div>
            </div>

        </div>
    </form>
</div>