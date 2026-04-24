<!doctype html>
<html>
    <head>
        <link rel="stylesheet" href="/css/printer.css" />
    </head>
    <body>
        <div id="wrap">
            <div id="verh_block">
                Гр.<br />
                <div class="div_under"> 
                    &nbsp;
                </div>
                <div class="div_under_small"> 
                    &nbsp;

                </div>
                <div class="div_under"> 
                    &nbsp;&nbsp;&nbsp;&nbsp;  <?= $record->fio ?> 
                </div>
                <div class="div_under_small"> 
                    &nbsp;
                </div>
                <div class="div_under"> 
                    &nbsp;
                </div>
                <br />
                <br />
                <br />
                <div class="div_in_verh">СПРАВКА (Ф-1)</div>
            </div>

            <div id="center_block">
                <div class="text_paragraph">
                    Справка выдана в том, что умерший(ая) ________________________
                </div>
            </div>
        </div>
    </body>
</html>