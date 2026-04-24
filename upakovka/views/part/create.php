<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\Part $model */

$this->title = 'Create Part';
$this->params['breadcrumbs'][] = ['label' => 'Parts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="part-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
