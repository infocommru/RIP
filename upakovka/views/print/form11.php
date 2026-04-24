<?php
$docnum = $_GET['docnum'];
$svid = "";
$number = $docnum;

$dd = explode('№', $docnum);
if (sizeof($dd) > 1) {
    $svid = $dd[0];
    $number = $dd[1];
}

$number = str_repeat('&nbsp;', 10) . $number . str_repeat('&nbsp;', 20);
$svid = str_repeat('&nbsp;', 10) . $svid . str_repeat('&nbsp;', 20);

$vidano = trim(strtr($_GET['vidano'], ["  " => " "]));
$vd = explode(" ", $vidano);
$vd_len = mb_strlen($vidano,'utf8');
///echo $vd_len;exit;
?>

<!doctype html>
<html>
    <head>
        <meta name="viewport" content="width=580px, initial-scale=1.0">
        <link rel="stylesheet" href="/css/bootstrap.css" />
        <link rel="stylesheet" href="/css/printer.css?a=<?= mt_rand(1, 111111) ?>" />
    </head>
    <body>
        <div style="margin:0 0;width: 1000px;" class="container">

            <div class="row">
                <div class="col-sm-7">
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

                        <br /><br />
                    </div>

                    <div class='logo_line  '>№ <span style='text-decoration: underline;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $_GET['nn'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> от<span style='text-decoration: underline;'>&nbsp;&nbsp;&nbsp;<?= $_GET['date'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>



                </div>
                <div class="col-sm-5"><br /><br /><br /><br />
                      <br />

                    <div class="div_under"> 
                        Гр. 
                        <?php

if((sizeof($vd)>1)&&(( mb_strlen($vd[1],'utf8')<=2)||(substr_count($vidano,'.')))){

echo $vidano;

}else{
echo $vd[0];
}


?>
                    </div>

<?php

if((sizeof($vd)>1)&&( mb_strlen($vd[1],'utf8')>2)&&(!substr_count($vidano,'.'))){

for ($i = 1; $i < sizeof($vd); $i++) {
    echo "<div class='div_under'> &nbsp;&nbsp;&nbsp;&nbsp; $vd[$i] </div>";
}

}else{
    for ($i = 0; $i < 3; $i++) {
    echo "<div class='div_under'> &nbsp;&nbsp;&nbsp;&nbsp;   </div>";
}
}
?>

                    <div class="div_under_small"> 
                        &nbsp;

                    </div>

                    <br /> <br /> <br />   
                    <div class="div_in_verh">СПРАВКА (Ф-1)</div>
                </div>
            </div>
            <br />
            <br />
            <div class="row">
                <div class="col-sm-10">
                    <div class="text-bigspace">
                        Справка выдана о том, что умерший(ая) 
                    </div>
                    <div class="undertext_real">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<?= $_GET['fio'] ?>
                    </div>
                    <div class="hinttext">
                        (фамилия, имя, отчество полностью)
                    </div>
                    <div  class="undertext">
                        <div style='display: block'>захоронен(а)</div><div style='display:inline-block' class="undertext_real"><?php echo str_repeat("&nbsp;", 4) ?><?= $_GET['zahr'] ?><?php echo str_repeat("&nbsp;", 10) ?></div>
                    </div> 
                </div>
            </div>
            <div class="row">
                <div class="col-sm-10">
                    <div class="hinttext">
                        (название кладбища, участок, ряд, место)
                    </div>
                    <div class="undertext">
                        дата захоронения
                        <span class="undertext_real">
<?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['rip_date'] ?><?php echo str_repeat("&nbsp;", 270) ?>
                        </span>
                    </div>
                    <div class="hinttext">
                        (год, месяц, число)
                    </div>
                    <div class="undertext">
                        по свидетельству о смерти <span class='undertext_real'><?= $svid ?></span> № <span class='undertext_real'><?= $number ?></span>                    
                    </div>
                    <div class="undertext">
                        место государственной регистрации смерти       
                        <span class="undertext_real">
<?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['zags'] ?><?php echo str_repeat("&nbsp;", 260) ?>
                        </span>            
                    </div>

                    <div class="undertext">
                        ответственное лицо

                        <span class="undertext_real"> 
<?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['author2'] ?><?php echo str_repeat("&nbsp;", 260) ?>
                        </span>

                    </div>

                    <div class="hinttext">
                        (фамилия, имя, отчество)
                    </div>
                    <div class="text-nospace">
                        Основание: связка <span class="undertext_real">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $_GET['svazka'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> , 
                        книга <span class="undertext_real">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $_GET['book_num'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> , стр. <span class="undertext_real">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $_GET['page_num'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> , п/п <span class="undertext_real">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= $_GET['pp'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                    </div>
                    <div class="text-nospace ">
                        Специалист по работе с архивом <span class="undertext_real"><?php echo str_repeat("&nbsp;", 10) ?><?= $_GET['author'] ?><?php echo str_repeat("&nbsp;", 20) ?>   </span> &nbsp;&nbsp;&nbsp;(_______________________)
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>