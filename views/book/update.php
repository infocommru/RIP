<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
$this->title = "Книга #" . $model->name . " (" . ($model->cemetery->name) . ")";
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="book-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
