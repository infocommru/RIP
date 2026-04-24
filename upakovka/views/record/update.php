<?php

use yii\helpers\Html;
use app\models\HelperImg;

/** @var yii\web\View $this */
/** @var app\models\Record $model */
$this->title = $model->book->name . ", запись #" . $model->id; //'Обновить запись: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Записи', 'url' => "/web/record/index?book=" . $model->book_id];
$this->params['breadcrumbs'][] = ['label' => '#' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="record-update">

    <h3><?= Html::encode($this->title) ?>
        <?php if ($model->updated_at): ?>
            (Обновлено <?= date("Y-m-d H:i", $model->updated_at) ?>)
        <?php endif; ?>
    </h3>
    <?php
    $im_url = "/upload/rip2/Южное кладбище/Св. 49/Кн. 284/0098.jpg";
    $im_url = "/upload/rip2/" . $model->filename;

    //if(file_exists("/var/www/html".$im_url)){
    //    phpinfo();exit;
    //}
    //$im_url = "/upload/rip3/0098.jpg";
    $im_url = str_replace(" ", "%20", $im_url);

    $items = HelperImg::getImages($model->filename);

    //echo HelperImg::findImages($model->filename);exit;
//print_r(HelperImg::getImages($model->filename));
    ?>
    <?php if (2 > 3): ?>

        <img id="imageZoomExtraPlus" src="<?= $im_url ?>" alt="A image to apply the ImageZoom plugin">
    <?php endif; ?>


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

<?php if ($model->fio): ?>
                jQuery.get("/web/api/spell", {txt: "<?= $model->fio ?>"}, function (dat) {
                    dat = jQuery.trim(dat);
                    jQuery('.fio_label').html(dat);
                    jQuery('.fio_label').show();
                    //console.log(dat)

                });
<?php endif; ?>

<?php if ($model->relative_fio): ?>
                jQuery.get("/web/api/spell", {txt: "<?= $model->relative_fio ?>"}, function (dat) {
                    dat = jQuery.trim(dat);
                    jQuery('.relative_fio_label').html(dat);
                    jQuery('.relative_fio_label').show();
                    //console.log(dat)

                });
<?php endif; ?>
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
    <?php
    dosamigos\gallery\Gallery::widget([
        'items' => $items,
        'clientEvents' => [
            'onslide' => 'function(index, slide) {
            //console.log(slide);
            console.log(index);
            current_img_index = index;
            jQuery("#record-filename").val(images_files[index]);
            jQuery(".current_gallery_elem").removeClass("current_gallery_elem");
            $(".gallery-item").eq(current_img_index).addClass("current_gallery_elem");
            var asrc = "lotus://"+model_id+","+images_files_short[index];
            jQuery("#open_img").attr("href",asrc);
            jQuery("#image_bottom").attr("src",images_files_urls[index]);
        }',
        //'onopen' => 'function(this) {console.log(this);}'
        ]
            ]
    );
    ?>

    <div class="container">
        <div class="row">
            <?php if ($prev): ?>
                <div class="col-sm">
                    <a id="go_b" href="/web/record/update?id=<?= $prev->id ?>" class="btn btn-link">&#129044; Назад</a>
                </div>
            <?php endif; ?>
            <?php if ($next): ?>
                <div class="col-sm">
                    <a id="go_f" href="/web/record/update?id=<?= $next->id ?>" class="btn btn-link">Вперед &#10132;</a>
                </div>
            <?php endif; ?>          
            <?php if (($first) && (false)): ?>
                <div class="col-sm">
                    <a id="go_ff" href="/web/record/update?id=<?= $first->id ?>" class="btn btn-info">Первая необработанная</a>
                </div>
            <?php endif; ?>
            <?php if ($model->updated_at): ?>
                <div class="col-sm">
                    <a  id="go_ff" href="/web/record-history/?record_id=<?= $model->id ?>" class="btn btn-info">История изменений</a>
                </div>
            <?php endif; ?>
            <div class="col-sm">
                <a target="_blank" id="go_new" href="/web/record/create?book_id=<?= $model->book_id ?>" class="btn btn-danger">Создать новую</a>
            </div>
        </div>
    </div>
    <hr />
    <?=
    $this->render('_form', [
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
