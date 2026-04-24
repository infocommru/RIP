<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\BookUpload $model */

$this->title = 'Update Book Upload: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Book Uploads', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="book-upload-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
