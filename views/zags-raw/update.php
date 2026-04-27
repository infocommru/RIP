<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ZagsRaw $model */

$this->title =   $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Опечатки ЗАГС', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить';
?>
<div class="zags-raw-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
