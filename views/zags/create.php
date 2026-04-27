<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Zags $model */

$this->title = 'Добавить ЗАГС';
$this->params['breadcrumbs'][] = ['label' => 'ЗАГС', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zags-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
