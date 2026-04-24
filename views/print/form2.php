<?php
$rip_date = '';
if (isset($_GET['rip_date']))
    $rip_date = $_GET['rip_date'];
$rip_year = '';
if (preg_match("#.*?(\d\d\d\d).*?#", $rip_date, $m)) {
    $rip_year = $m[1];
}

if (isset($_GET['dead_year']))
    $rip_year = $_GET['dead_year'];

//echo $rip_date;
//exit;

$vidano = trim(strtr($_GET['vidano'], ["  " => " "]));
$vd = explode(" ", $vidano);
$vd_len = mb_strlen($vidano, 'utf8');
?>
<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="/css/bootstrap.css" />
        <link rel="stylesheet" href="/css/printer.css?a=<?= mt_rand(1, 111111) ?>" />
        <link rel="stylesheet" href="/css/bprinter.css?a=<?= mt_rand(1, 111111) ?>" />

    </head>
    <body  >
        <div style="margin:0 auto;width:1000px;" class="container">

            <div class="row">
                <div class="col-sm-7 col-print-7">




                    <div class='logo_line'><img width='64px' src='/img/print_logo.png' /></div>
                    <div class='logo_line logo_line_upper'> 
                        Правительство Санкт-Петербурга <br />
                        Комитет по промышленной политике,<br />
                        Инновациям и торговле Санкт-Петербурга
                        <br /><br />
                    </div>
                    <div class='logo_line logo_line_upper logo_line_bold'> 
                        Санкт-Петербургское<br />
                        Государственное Казенное Учреждение<br />
                        &laquo;Специализированная служба санкт-петербурга<br />
                        по вопросам похоронного дела&raquo;
                    </div>

                    <div class='logo_line_small '> 
                        1-я Советская ул., д. 8, Санкт-Петербург, 191036 <br />
                        E-mail: info@svpd.cipit.gov.spb.ru <br />
                        https://svpd.cipit.gov.spb.ru <br />
                        Тел. (812) 241-24-24, Факс (812) 241-24-21 <br />
                        ОКПО 96728516 ОКОГУ 2300216 ОГРН 5067847213033 <br />
                        ИНН/КПП 7842340459/784201001

                    </div>

                    <div class='logo_line  '>№ <span style='text-decoration: underline;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $_GET['nn'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> от<span style='text-decoration: underline;'>&nbsp;&nbsp;&nbsp;<?= $_GET['date'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>






                </div>
                <div class="col-sm-5 col-print-5">
                    <br />

                    <div class="div_under"> 
                        Гр. 
                        <?php
                        if ((sizeof($vd) > 1) && (( mb_strlen($vd[1], 'utf8') <= 2) || (substr_count($vidano, '.')))) {

                            echo $vidano;
                        } else {
                            echo $vd[0];
                        }
                        ?>
                    </div>

                    <?php
                    if ((sizeof($vd) > 1) && ( mb_strlen($vd[1], 'utf8') > 2) && (!substr_count($vidano, '.'))) {

                        for ($i = 1; $i < sizeof($vd); $i++) {
                            echo "<div class='div_under'> &nbsp;&nbsp;&nbsp;&nbsp; $vd[$i] </div>";
                        }
                    } else {
                        for ($i = 0; $i < 3; $i++) {
                            echo "<div class='div_under'> &nbsp;&nbsp;&nbsp;&nbsp;   </div>";
                        }
                    }
                    ?>

                    <div class="div_under_small"> 
                        &nbsp;

                    </div>
                    <br />
                    <br /><br /><br />
                    <div class="div_in_verh div_in_verh_spravka">СПРАВКА (Ф-2)</div>
                </div>
            </div>
            <br />  <br />  <br />

            <div class="row">
                <div class="col-sm-10">
                    <div class="text-nospace">
                        &nbsp;&nbsp;&nbsp;&nbsp;Архив по учёту захоронений Санкт-Петербургского государственного казенного учреждения<br /> «Специализированная служба Санкт-Петербурга по вопросам похоронного дела»<br /> не имеет данных 
                        о захоронении
                    </div>
                    <div class="undertext_real">
                        <?php echo str_repeat("&nbsp;", 10) ?><?= $_GET['fio'] ?><?php echo str_repeat("&nbsp;", 10) ?>
                    </div>
                    <div class="hinttext">
                        (фамилия, имя, отчество полностью)
                    </div>
                    <div class="text-nospace">
                        на кладбище <span class="undertext_real"><?php echo str_repeat("&nbsp;", 7) ?><?= $_GET['cemetery'] ?><?php echo str_repeat("&nbsp;", 5) ?></span>
                        в <span class="undertext_real"><?php echo str_repeat("&nbsp;", 10) ?><?= $rip_year ?><?php echo str_repeat("&nbsp;", 10) ?></span> году
                    </div>
                    <div class="col-sm-12" style='overflow: hidden;'>
                        <span class="undertext_real" style="display: inline-block"> 
                            <?php echo str_repeat("&nbsp;", 10) . strtr(trim($_GET['comment']), [" " => '&nbsp;']) . str_repeat("&nbsp;", 500)
                            ?>
                        </span>
                    </div></div>

                <?php if (isset($_GET['print_comment'])): ?>

                    <div class="col-sm-12" >
                        <span class="comment_ips" style="display: inline-block;"> 
                            Справка сформирована на основе ИПС "Поиск захоронений"
                        </span>
                    </div>

                <?php endif; ?>

                <div class="text-nospace undertext">
                    Специалист по работе с архивом <span class="undertext_real"><?php echo str_repeat("&nbsp;", 15) ?> <?php echo str_repeat("&nbsp;", 5) ?></span> (<span class="undertext_real">&nbsp;&nbsp;&nbsp;<?= $_GET['author'] ?>&nbsp;&nbsp;&nbsp;</span>)
                </div>

            </div>
        </div>

    </div>
</body>
</html>