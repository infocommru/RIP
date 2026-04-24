<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\RecordHistory $model */

$this->title = 'Create Record History';
$this->params['breadcrumbs'][] = ['label' => 'Record Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
