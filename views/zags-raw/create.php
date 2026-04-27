<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ZagsRaw $model */

$this->title = 'Create Zags Raw';
$this->params['breadcrumbs'][] = ['label' => 'Zags Raws', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zags-raw-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
