<?php

use yii\helpers\Html;
use app\models\HelperImg;
use app\models\HelperLevoshkin;

/** @var yii\web\View $this */
/** @var app\models\Record $model */
$model = $record;
$title = $part->cemetery->name . ', партия #' . $part->id;

$this->title = $title; //$model->book->name . ", запись #" . $model->id; //'Обновить запись: ' . $model->id;
//$this->params['breadcrumbs'][] = ['label' => 'Записи', 'url' => "/web/record/index?book=" . $model->book_id];
//$this->params['breadcrumbs'][] = ['label' => '#' . $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="part-update">

    <h3><?= Html::encode($this->title) ?>

    </h3>
    <h5><?= HelperLevoshkin::getPartRecordStatuses()[$part_record->status] ?></h5>
    <h5>Невалид/Валид/Есть вопрос/Всего: 
        <?php
        echo $invalid_count;
        echo ' [' . round($invalid_count * 100.0 / $total_count) . '%]';
        echo ' | ';
        echo $valid_count;
        echo ' [' . round($valid_count * 100.0 / $total_count) . '%]';

        echo ' | ';

        echo $vopros_count;
        echo ' [' . round($vopros_count * 100.0 / $total_count) . '%]';

        echo ' | ';
        echo $total_count;
        echo ' | ';
        ?>

    </h5>
    <?php
//$im_url = "/upload/rip2/Южное кладбище/Св. 49/Кн. 284/0098.jpg";
    $im_url = "/upload/rip2/" . $model->filename;
//$im_url = "/upload/rip3/0098.jpg";
//$im_url = str_replace(" ", "%20", $im_url);

    $items = HelperImg::getImages($model->filename);
//print_r(HelperImg::getImages($model->filename));
    ?>

    <script>
        var model_id = <?= $model->id ?>;
        var images_files_urls = [];
        var images_files = [];
        var images_files_short = [];
        var current_img_index = 2;
        var pnum = 1;
        pnum = <?php echo (isset($_GET['pnum'])) ? $_GET['pnum'] : 1; ?>;

<?php
foreach ($items as $item) {
    echo "images_files_urls.push('" . $item['url'] . "');";
    echo "images_files.push('" . $item['src2'] . "');";
    echo "images_files_short.push('" . $item['src3'] . "');";
}
?>

        window.onload = () => {
            var asrc = "lotus://" + model_id;
            jQuery("#open_img").attr("href", asrc);
            open_image_bottom(pnum);
            jQuery(".gallery-item").eq(current_img_index).addClass('current_gallery_elem');
            jQuery(".img_gal").eq(current_img_index).addClass('current_gallery_elem');




        }

        function open_image_bottom(num) {
            for (var i = 1; i <= 5; i++) {
                jQuery('#image_bottom').removeClass('bottom_img_offet' + i);

            }

            jQuery('#image_bottom').addClass('bottom_img_offet' + num);
            jQuery('.pagenum').removeClass('active');
            jQuery('.pagenum' + num).addClass('active');
            jQuery('#pageNum').val(num);
        }


        hotkeys('alt+t,alt+r,alt+s,alt+n', function (event, handler) {
            //alert(111);
            switch (handler.key) {
                case 'alt+t':
                    if (jQuery("#go_f")) {
                        var href = jQuery("#go_f").attr('href');
                        location.assign(href);
                    }
                    break;
                case 'alt+r':
                    if (jQuery("#go_b")) {
                        var href = jQuery("#go_b").attr('href');
                        location.assign(href);
                        //alert('you pressed ctrl+b!');
                    }
                    break;
                case 'alt+s':
                    var form = jQuery('#w1');
                    form.submit();
                    //alert('you pressed r!');
                    break;
                case 'alt+n':
                    var href = jQuery("#go_new").attr('href');
                    //location.assign(href);
                    window.open(href, '_blank');
                    break;
                default:
                //alert(event);
            }
        });


        function help_region() {
            var rg = jQuery('.region_valid').html();
            rg = jQuery.trim(rg);
            jQuery('#record-zags').val(rg);
            //alert(rg);
        }

        function help_fio() {
            var fio = jQuery('.fio_label').html();
            fio = jQuery.trim(fio);
            jQuery('#record-fio').val(fio);
        }

        function help_relative_fio() {
            var fio = jQuery('.relative_fio_label').html();
            fio = jQuery.trim(fio);
            jQuery('#record-relative_fio').val(fio);
        }


        function click_image(index) {
            //alert(num);
            jQuery('.img_gal').removeClass('current_gallery_elem');
            jQuery('.img_gal' + index).addClass('current_gallery_elem');
            jQuery("#record-filename").val(images_files[index]);
            var asrc = "lotus://" + model_id + "," + images_files_short[index];
            jQuery("#open_img").attr("href", asrc);
            jQuery("#image_bottom").attr("src", images_files_urls[index]);

        }
    </script>
    <?php
//print_r($items);
    for ($i = 0; $i < sizeof($items); $i++) {
        $item = $items[$i];
        $num = $i + 1;

        echo "<a class='img_gal img_gal$i ' href='javascript:click_image($i)'><img src='$item[src]' /></a>";

        //print_r($item);
    }
    ?>

    <div class="container">
        <div class="row">
            <?php if ($prev): ?>
                <div class="col-sm">
                    <a id="go_b" href="/web/part/update-record?part_id=<?= $part->id ?>&record_id=<?= $prev->id ?>" class="btn btn-link">&#129044; Назад</a>
                </div>
            <?php endif; ?>
            <?php if (($next)&&(0)): ?>
                <div class="col-sm">
                    <a id="go_f" href="/web/part/update-record?part_id=<?= $part->id ?>&record_id=<?= $next->id ?>" class="btn btn-link">Вперед &#10132;</a>
                </div>
            <?php endif; ?>          
            <?php if ($first): ?>
                <div class="col-sm">
                    <a id="go_ff" href="/web/part/update-record?part_id=<?= $part->id ?>&record_id=<?= $first->record_id ?>" class="btn btn-info">Первая необработанная</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="row">
            <div class="col-sm">
                <a   href="/web/part/update-record?part_id=<?= $part->id ?>&record_id=<?= $current->id ?>&status=2" class="btn btn-danger">Плохая запись</a>
            </div>
            <div class="col-sm">
                <a   href="/web/part/update-record?part_id=<?= $part->id ?>&record_id=<?= $current->id ?>&status=4" class="btn btn-warning">Есть вопрос</a>
            </div>
            <div class="col-sm">
                <a   href="/web/part/update-record?part_id=<?= $part->id ?>&record_id=<?= $current->id ?>&status=3" class="btn btn-success">Хорошая запись</a>
            </div>
        </div>
    </div>
    <hr />
    <?=
    $this->render('_form_record', [
        'model' => $model,
        'is_create' => false,
    ])
    ?>
    <hr />

    <div class="container">
        <div class="row">
            <div class="col-sm-12">

                <ul class="pagination">

                    <li class="page-item pagenum pagenum1 active" aria-current="page"><a class="page-link" href="javascript:open_image_bottom(1)"  >1</a></li>
                    <li class="page-item pagenum pagenum2"><a class="page-link" href="javascript:open_image_bottom(2)"  >2</a></li>
                    <li class="page-item pagenum pagenum3"><a class="page-link" href="javascript:open_image_bottom(3)"  >3</a></li>
                    <li class="page-item pagenum pagenum4"><a class="page-link" href="javascript:open_image_bottom(4)"  >4</a>
                    </li>
                    <li class="page-item pagenum pagenum5"><a class="page-link" href="javascript:open_image_bottom(5)"  >5</a>
                    </li>
                </ul>

            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">

                <div id='image_bottom_top' style='width:100%;overflow-y: hidden;min-height: 900px;'>
                    <img style="width: 100%; min-height: 900px;" src='<?= $im_url ?>' id='image_bottom'>
                </div>



            </div>
        </div>
    </div>

</div>
