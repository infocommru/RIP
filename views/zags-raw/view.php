<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\ZagsRaw $model */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Опечатки ЗАГС', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="zags-raw-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php
        Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'ЗАГС',
                'value' => function ($model) {
                    if (!$model->zags_id)
                        return '-';
                    return $model->zags->name;
                }
            ],
            'name',
            'cnt',
        ],
    ])
    ?>
    <hr />

    <?php
    $record = new \app\models\Record();
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $record,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'numReg',
            'numLiteral',
            'fio',
            'age',
            'death_date',
            'rip_date',
            'docnum',
            'zags',
            //'riper',
            'area_num',
            'row_num',
            'rip_num',
            'relative_fio',
            'filename',
            'comment:ntext',
            [
                'label' => '',
                'value' => function ($model) {
                    return "<a target='_blank' href='/web/record/update?id=" . $model->id . "'   > <img width='24px' src='/img/edit.png'> </a>";
                },
                'format' => 'html'
            ],
        ],
    ]);
    ?>
</div>
