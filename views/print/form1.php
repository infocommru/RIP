<?php
$vidano = trim(strtr($_GET['vidano'], ["  " => " "]));

$vd = explode(" ",$vidano);

?><!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="/css/bootstrap.css" />
        <link rel="stylesheet" href="/css/printer.css?a=<?= mt_rand(1, 111111) ?>" />
    </head>
    <body>
        <div class="container">

            <div class="row">
                <div class="col-sm-7">

                </div>
                <div class="col-sm-5">
                    Гр.<br />
                    <div class="div_under"> 
                        &nbsp;
 
                    </div>
                    <div class="div_under"> 
                        &nbsp;&nbsp;&nbsp;&nbsp;  <?= $_GET['vidano'] ?> 
                    </div>
                    <div class="div_under"> 
                        &nbsp;
                    </div>
                    <br />
                    <div class="div_in_verh">СПРАВКА (Ф-1)</div>
                </div>
            </div>
            <br />
            <div class="row">
                <div class="col-sm-12">
                    <div class="text-bigspace">
                        Справка выдана о том, что умерший(ая) 
                    </div>
                    <div class="undertext">
                        <?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['fio'] ?><?php echo str_repeat("&nbsp;", 140) ?>
                    </div>
                    <div class="hinttext">
                        (фамилия, имя, отчество полностью)
                    </div>
                    <div class="text-bigspace">
                        захоронен(а)
                    </div>
                    <div class="undertext">
                        <?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['zahr'] ?><?php echo str_repeat("&nbsp;", 80) ?>
                    </div>
                    <div class="hinttext">
                        (название кладбища, участок, ряд, место)
                    </div>
                    <div class="text-bigspace">
                        дата захоронения
                    </div>
                    <div class="undertext">
                        <?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['rip_date'] ?><?php echo str_repeat("&nbsp;", 150) ?>
                    </div>
                    <div class="hinttext">
                        (год, месяц, число)
                    </div>
                    <div class="text-bigspace">
                        по свидетельству о смерти №                     
                    </div>
                    <div class="undertext">
                        <?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['docnum'] ?><?php echo str_repeat("&nbsp;", 150) ?>
                    </div>
                    <div class="text-bigspace">
                        место государственной регистрации смерти                   
                    </div>
                    <div class="undertext">
                        <?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['zags'] ?><?php echo str_repeat("&nbsp;", 150) ?>
                    </div>
                    <div class="text-bigspace">
                        ответственное лицо
                    </div>
                    <div class="undertext">
                        <?php echo str_repeat("&nbsp;", 20) ?><?= $_GET['author2'] ?><?php echo str_repeat("&nbsp;", 150) ?>
                    </div>
                    <div class="hinttext">
                        (фамилия, имя, отчество)
                    </div>
                    <div class="text-nospace">
                        Основание: связка <span class="undertext">&nbsp;<?= $_GET['svazka'] ?>&nbsp;</span> , 
                        книга <span class="undertext">&nbsp;<?= $_GET['book_num'] ?>&nbsp;</span> , стр. <span class="undertext">&nbsp;<?= $_GET['page_num'] ?>&nbsp;</span> , п/п <span class="undertext">&nbsp;<?= $_GET['pp'] ?>&nbsp;</span>
                    </div>
                    <div class="text-nospace">
                        Специалист по работе с архивом <span class="undertext"><?php echo str_repeat("&nbsp;", 30) ?><?= $_GET['author'] ?><?php echo str_repeat("&nbsp;", 80) ?></span>
                    </div>
                    <div class="text-nospace">
                        (_______________________)
                    </div>
                </div>
            </div>

        </div>
    </body>
</html>