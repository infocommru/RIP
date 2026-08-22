<?php

use app\models\Helper;
use app\models\User;
use app\models\Record;
use yii\helpers\Url;

/**
 * @param int $cemetery_id
 * @return int
 */
function get_page($cemetery_id) {
    $pages = explode(';', $_GET['pager']);
    $curpage = 1;
    foreach ($pages as $p) {
        $pp = explode(",", $p);
        if ($pp[0] == (string)$cemetery_id)
            $curpage = $pp[1];
    }

    return intval($curpage);
}

$user = app\models\User::findIdentity(Yii::$app->user->id);

$url_export = $_SERVER['REQUEST_URI'];
$url_export = strtr($url_export, ['/search' => '/search/export']);
?><div class="row">
    <div class="col-sm-12">
        <div id="tabs">
            <ul>
                <?php
                /** @var false|array<string, mixed> $data */
                foreach ($data as $list):
                    ?>
                    <li><a href="#tabs-<?= $list['id'] ?>"><?= $list['name'] ?></a></li>
                <?php endforeach; ?>
            </ul>

            <?php
            /** @var false|array<string, mixed> $data */
            foreach ($data as $list):
                $pages = 1;
                $page = 1;
                ?>
                <div id="tabs-<?= $list['id'] ?>">
                    <?php
                    if ($list['counter'] > 100):
                        $pages = ceil((double)$list['counter'] / 100);
                        $page = get_page(intval($list['id']));
                        ?>
                    <?php endif; ?>
                    <h5>Всего записей: <?= $list['counter'] ?>. Выгрузить <a href="<?= $url_export . "&c_id=" . $list['id'] ?>">excel</a></h5>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Номер</th>
                                <th>ФИО</th>
                                <th>Возраст</th>
                                <th>Дата смерти</th>
                                <th>Дата захоронения</th>
                                <th>ЗАГС</th>
                                <th>Захоронение</th>
                                <th>Номер документа</th>
                                <th>Номер участка</th>
                                <th>Номер ряда</th>
                                <th>Номер могилы</th>
                                <th>Родственники</th>
                                <th>Доп. инфо</th>
                                <th> </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i = 0; $i < sizeof($list['data']); $i++) {
                                $elem = $list['data'][$i]['_source'];
                                $num = $i + 1;
                                $regnum = $elem['regnum'];

                                if ($page > 1)
                                    $num += 100 * ($page - 1);

                                $fio = $elem['fio_display'];
                                $age = $elem['age'];
                                $dead_year = Helper::formatDate($elem['dead_date']);
                                $rip_year = Helper::formatDate($elem['rip_date']);
                                $zags = $elem['zags'];

                                $rip_style = app\models\Record::ripStyleTypes()[$elem['rip_style']];
                                if ((isset($elem['book_rip_style'])) && ($elem['book_rip_style'])) {
                                    $rip_style = app\models\Record::ripStyleTypes()[$elem['book_rip_style']];
                                }

                                $docnum = $elem['docnum'];
                                $areanum = $elem['areanum'];
                                $rownum = $elem['rownum'];
                                $ripnum = $elem['ripnum'];
                                $relative = $elem['relative'];
                                $comment = $elem['comment'];

                                $dopInfo = "<span style='font-size:13px;'>св. $elem[svazka_num], кн. $elem[book_num], стр. $elem[page_num], строка: $elem[page_punkt]";

                                if ((isset($elem['comment_book'])) && ($elem['comment_book'])) {
                                    $comment .= " " . $elem['comment_book'];
                                }

                                if ($comment)
                                    $dopInfo .= "<br />$comment";

                                $dopInfo .= "<br /><a class='link-primary' target='_blank' href='/web/search/book-cover?book_id=$elem[book_id]'>обложка</a>";
                                $dopInfo .= "</span>";

                                $filelink = '';
                                if ($elem['filename']) {
                                    $im_url = Url::to(Yii::getAlias("@webimages/$elem[filename]"));
                                    $im_url = str_replace("\\", "/", $im_url);
                                    $filelink = "<a target='_blank' href='$im_url'><img src='/img/view.png' width='24px' /></a>";
                                }

                                if ($user->role != -4) {

                                    if (!$elem['vopros'])
                                        $filelink .= "<a id='vopros$elem[record_id]' title='требуется уточнить данные'   href='javascript:vopros(" . $elem['record_id'] . ");'><img src='/img/vopros.png' width='24px' /></a>";

                                    if ($elem['updated_at']) {
                                        $filelink .= "<a target='_blank' id='vopros$elem[record_id]' title='история изменения'   href='/web/record-history/?record_id=" . $elem['record_id'] . "'><img src='/img/history.png' width='24px' /></a>";
                                    }
                                    $filelink .= "<a target='_blank' id='print$elem[record_id]' title='печать'   href='/web/print/?record_id=$elem[record_id]'><img src='/img/print.png' width='24px' /></a>";
                                }

                                if ($user->role != 2) {
                                    $filelink .= "<a target='_blank' id='print$elem[record_id]' title='редактировать'   href='/web/record/update/?id=$elem[record_id]'><img src='/img/edit.png' width='24px' /></a>";
                                }

                                //////////////////////////////////
                                echo "<tr>";
                                echo "<td>$num</td>";
                                echo "<td>$regnum</td>";
                                echo "<td>$fio</td>";
                                echo "<td>$age</td>";
                                echo "<td>$dead_year</td>";
                                echo "<td>$rip_year</td>";
                                echo "<td>$zags</td>";
                                echo "<td>$rip_style</td>";
                                echo "<td>$docnum</td>";
                                echo "<td>$areanum</td>";
                                echo "<td>$rownum</td>";
                                echo "<td>$ripnum</td>";
                                echo "<td>$relative</td>";
                                echo "<td>$dopInfo</td>";
                                echo "<td>$filelink</td>";
                                echo "</tr>";
                            }
                            ?>

                        </tbody>
                    </table>
                    <?php if ($pages > 1) { ?>
                        <div class="row">
                            <div class="col-sm-12">

                                <ul class="pagination">
                                    <?php
                                    $current_page = get_page(intval($list['id']));
                                    if ($pages <= 30) {
                                        for ($i = 1; $i <= $pages; $i++) {

                                            $active = '';
                                            if ($i == $current_page)
                                                $active = 'active';
                                            echo <<<HERE
<li class="page-item pagenum pagenum$i $active" aria-current="page"><a class="page-link" href="javascript:next_page({$list['id']},$i);"  >$i</a></li>
HERE;
                                        }
                                    } else {

                                        $pstart = $current_page - 10;
                                        $pend = $current_page + 10;

                                        if ($pstart < 1)
                                            $pstart = 1;
                                        if ($pend > $pages)
                                            $pend = $pages;

                                        $pend_break = false;

                                        if ($pend > $pages)
                                            $pend = $pages;

                                        for ($i = $pstart; $i <= $pend; $i++) {
                                            $active = '';
                                            if ($i == $current_page)
                                                $active = 'active';

                                            $lname = $i;
                                            if (($pstart > 1) && ($i == $pstart))
                                                $lname = '<< ' . $i;
                                            if (($pend < $pages) && ($i == $pend))
                                                $lname = $i . ' >>';

                                            echo <<<HERE
<li class="page-item pagenum pagenum$i $active" aria-current="page"><a class="page-link" href="javascript:next_page({$list['id']},$i);"  >$lname</a></li>
HERE;
                                        }
                                    }
                                    ?>

                                </ul>

                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>            
            <?php endforeach; ?>
        </div> 
    </div>
</div>
