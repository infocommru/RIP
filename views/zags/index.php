<?php

use app\models\Zags;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Текущие ЗАГС';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zags-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        //similar_text("Иванов", "Кванов", $p);
        //echo $p;
        ?>

        <?= Html::a('Добавить ЗАГС', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            'description:ntext',
            //'deleted',
            [
                'label' => 'Активно',
                'value' => function ($model) {
                    return Zags::delStatuses()[$model->deleted];
                }
            ],
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Zags $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]);
    ?>


</div>
