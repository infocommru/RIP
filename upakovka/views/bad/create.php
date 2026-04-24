<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Record2 $model */

$this->title = 'Create Record2';
$this->params['breadcrumbs'][] = ['label' => 'Record2s', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record2-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
