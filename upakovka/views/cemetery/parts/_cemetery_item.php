<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
?>
<div class="post">
    <h4><a href="/web/site/viewcemetery/<?= $model->id ?>"><?= Html::encode($model->name) ?></a></h4>
    <?= nl2br($model->description) ?>
</div>