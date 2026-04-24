<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Cemetery $model */

$this->title = 'Добавить кладбище';
$this->params['breadcrumbs'][] = ['label' => 'Кладбища', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cemetery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
