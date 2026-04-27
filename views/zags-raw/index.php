<?php

use app\models\ZagsRaw;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Опечатки в ЗАГС';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="zags-raw-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            //'zags_id',
            [
                'label' => 'ЗАГС',
                'value' => function ($model) {
                    if (($model->zags_simular_id < 0) || (!$model->zags_id) || ($model->zags_id < 0))
                        return '-';
                    $zags = \app\models\Zags::find()->andWhere(['id' => $model->zags_id])->one();
                    return $zags->name;
                }
            ],
            'name',
            [
                'label' => 'Ближайший ЗАГС',
                'value' => function ($model) {
                    if (($model->zags_simular_id < 0) || (!$model->zags_simular_id))
                        return '-';
                    $zags = \app\models\Zags::find()->andWhere(['id' => $model->zags_simular_id])->one();
                    return $zags->name;
                }
            ],
            'percent',
            'cnt',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, ZagsRaw $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]);
    ?>


</div>
