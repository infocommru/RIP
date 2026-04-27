<?php

use app\models\Helper;

function dat_format($date) {
    $date = strtr($date, ['00:00:00' => '']);
    $date = trim($date);

    return $date;
}

function get_page($cemetery_id) {
    $pages = explode(';', $_GET['pager']);
    $curpage = 1;
    foreach ($pages as $p) {
        $pp = explode(",", $p);
        if ($pp[0] == $cemetery_id)
            $curpage = $pp[1];
    }

    return $curpage;
}

$user = \app\models\User::findIdentity(Yii::$app->user->id);

$url_export = $_SERVER['REQUEST_URI'];
$url_export = strtr($url_export, ['/search' => '/search/export']);
?><div class="row">
    <div class="col-sm-12">
        <div id="tabs">
            <ul>
                <?php
                foreach ($data as $key => $list):
                    $kk = explode(",", $key);
                    ?>
                    <li><a href="#tabs-<?= $kk[0] ?>"><?= $kk[1] ?></a></li>
                <?php endforeach; ?>
            </ul>

            <?php
            foreach ($data as $key => $list):
                $pages = 1;
                $page = 1;
                $kk = explode(",", $key);
                ?>
                <div id="tabs-<?= $kk[0] ?>">
                    <?php
                    if ($kk[2] > 100):
                        $pages = ceil(1.0 * $kk[2] / 100);
                        $page = get_page($kk[0]);
                        //echo $pages;
                        //echo ';'.$kk[2];
                        ?>
                    <?php endif; ?>
                    <h5>Всего записей: <?= $kk[2] ?>. Выгрузить <a href="<?= $url_export . "&c_id=" . $kk[0] ?>">excel</a>, 
                        <a href="<?= $url_export . "&c_id=" . $kk[0] ?>&csv=1">csv</a></h5>

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
                            $zagsIds = Helper::regionsWithIds();

                            for ($i = 0; $i < sizeof($list); $i++) {
                                $elem = $list[$i];
                                $num = $i + 1;
                                $regnum = '';
                                if (isset($elem['regnum']))
                                    $regnum = $elem['regnum'];

                                $regnum = $elem['record']['numReg'];
                                if (!$regnum)
                                    $regnum = $elem['record']['numLiteral'];

                                if ($page > 1)
                                    $num += 100 * ($page - 1);
                                $fio = $elem['fam'] . ' ' . $elem['nam'] . ' ' . $elem['ot'];
                                $fio = $elem['record']['fio'];
                                $age = $elem['record']['age'];
                                $dead_year = Helper::formatDate($elem['record']['death_date']);
                                $rip_year = Helper::formatDate($elem['record']['rip_date']);
                                //$zags = \app\models\Helper::regionToText($elem['zags_num']);
                                #$zags = $elem['zags_num'];
                                #if ($zags == '-')
                                $zags = $elem['zags'];
                                if ($elem['record']['zags_id'])
                                    $zags = $zagsIds[$elem['record']['zags_id']];

                                $rip_style = \app\models\Record::ripStyleTypes()[$elem['record']['rip_style']];
                                if ((isset($elem['book_rip_style'])) && ($elem['book_rip_style'])) {
                                    $rip_style = \app\models\Record::ripStyleTypes()[$elem['book_rip_style']];
                                }

                                $docnum = $elem['record']['docnum'];
                                $areanum = $elem['record']['area_num'];
                                $rownum = $elem['record']['row_num'];
                                $ripnum = $elem['record']['rip_num'];
                                $relative = $elem['record']['relative_fio'];

                                //$record = app\models\Record::find()->andWhere(['id'=>$elem['record_id']])->one();

                                $comment = $elem['record']['comment'];

                                $dopInfo = "<spac style='font-size:13px;'>св. $elem[svazka_num], кн. $elem[book_num], стр. $elem[page_num], строка: $elem[page_punkt]";

                                if ((isset($elem['comment_book'])) && ($elem['comment_book'])) {
                                    $comment .= " " . $elem['comment_book'];
                                }


                                if ($comment)
                                    $dopInfo .= "<br />$comment";

                                $dopInfo .= "<br /><a class='link-primary' target='_blank' href='/web/search/book-cover/?record_id=$elem[record_id]'>обложка</a>";

                                $dopInfo .= "</span>";

                                //$flink = "/"
                                $filelink = '';
                                if ($elem['record']['filename']) {

                                    $im_url = "/upload/rip2/" . $elem['record']['filename'];

                                    $im_url = str_replace(" ", "%20", $im_url);

                                    $filelink = "<a target='_blank' href='$im_url'><img src='/img/view.png' width='24px' /></a>";
                                }

                                if ($user->role != -4) {

                                    if (!$elem['record']['vopros'])
                                        $filelink .= "<a id='vopros$elem[record_id]' title='требуется уточнить данные'   href='javascript:vopros(" . $elem['record_id'] . ");'><img src='/img/vopros.png' width='24px' /></a>";

                                    if ($elem['record']['updated_at']) {
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
                                    $current_page = get_page($kk[0]);
                                    if ($pages <= 30) {
                                        for ($i = 1; $i <= $pages; $i++) {

                                            $active = '';
                                            if ($i == $current_page)
                                                $active = 'active';
                                            echo <<<HERE
<li class="page-item pagenum pagenum$i $active" aria-current="page"><a class="page-link" href="javascript:next_page($kk[0],$i);"  >$i</a></li>
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
<li class="page-item pagenum pagenum$i $active" aria-current="page"><a class="page-link" href="javascript:next_page($kk[0],$i);"  >$lname</a></li>
HERE;
                                        }
                                    }
                                    ?>

                                </ul>

                            </div>
                        </div>

                        <?php
                    } else {
                        //echo '000';
                    }
                    ?>
                </div>            
<?php endforeach; ?>


        </div> 
    </div>
</div>
