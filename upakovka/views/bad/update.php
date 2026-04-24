<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Record2 $model */

$this->title = 'Update Record2: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Record2s', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="record2-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
