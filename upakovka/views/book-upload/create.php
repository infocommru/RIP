<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BookUpload $model */

$this->title = 'Create Book Upload';
$this->params['breadcrumbs'][] = ['label' => 'Book Uploads', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-upload-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
