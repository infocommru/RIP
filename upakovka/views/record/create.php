<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Record $model */
$this->title = 'Новая запись (' . $book->name . ')';
$this->params['breadcrumbs'][] = ['label' => $book->name, 'url' => ['index', 'book' => $book->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    $this->render('_form', [
        'model' => $model,
        'is_create' => true,
    ])
    ?>

</div>
