<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RecordHistory $model */

$this->title = 'Update Record History: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Record Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="record-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
