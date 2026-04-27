<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Zags $model */

$this->title = 'Обновить ЗАГС: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'ЗАГС', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="zags-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
