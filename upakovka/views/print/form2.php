<?php
$rip_date = $_GET['rip_date'];
$rip_year = '';
if (preg_match("#.*?(\d\d\d\d).*?#", $rip_date, $m)) {
    $rip_year = $m[1];
}
//echo $rip_date;
//exit;

$vidano = trim(strtr($_GET['vidano'], ["  " => " "]));
$vd = explode(" ", $vidano);
$vd_len = mb_strlen($vidano,'utf8');

?>
<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="/css/bootstrap.css" />
        <link rel="stylesheet" href="/css/printer.css?a=<?= mt_rand(1, 111111) ?>" />
    </head>
    <body  >
        <div style="margin:0 0;width:1000px;" class="container">

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

<div class='logo_line  '>№ <span style='text-decoration: underline;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$_GET['nn']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span> от<span style='text-decoration: underline;'>&nbsp;&nbsp;&nbsp;<?=$_GET['date']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></div>






                </div>
                <div class="col-sm-5">
                    <br /><br /><br /><br />
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
                    <br />
                    <br />
                    <div class="div_in_verh">СПРАВКА (Ф-2)</div>
                </div>
            </div>
            <br />
            <br />
            <br />
            <br />

            <div class="row">
                <div class="col-sm-10">
                    <div class="text-nospace">
                        &nbsp;&nbsp;&nbsp;&nbsp;Архив по учёту захоронений Санкт-Петербургского государственного казенного учреждения «Специализированная служба Санкт-Петербурга по вопросам похоронного дела» не имеет данных 
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
                    <div class="text-nospace undertext">
                        Специалист по работе с архивом <span class="undertext_real"><?php echo str_repeat("&nbsp;", 5) ?><?= $_GET['author'] ?><?php echo str_repeat("&nbsp;", 5) ?></span> (____________________)
                    </div>

                </div>
            </div>

        </div>
    </body>
</html>