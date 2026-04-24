<?php

use app\models\Record;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = $book->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить запись', ['create', 'book_id' => $book_id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Экспорт CSV', "/web/record/export-csv?id=" . $book_id, ['class' => 'btn btn-warning']) ?>
        <?= Html::a('Сбросить фильтры', "/web/record/index?book=" . $book_id, ['class' => 'btn btn-info']) ?>
    </p>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            //'book_id',
            [
                'label' => 'Обновлено',
                'value' => function ($model) {
                    if (!$model->updated_at)
                        return '-';
                    return date("Y-m-d H:i");
                }
            ],
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
            //'rip_style',
            [
                'label' => 'Захоронение',
                'value' => function ($model) {
                    return \app\models\Record::ripStyleTypes()[$model->rip_style];
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                //'contentOptions' => 
                'header' => '',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'delete' => function ($model) {
                        $user = Yii::$app->user->identity;
                        return true;
                        return $user->role == 1;
                    },
                    'view' => function ($model) {
                        //if($model->user_id)return false;
                        //$user = Yii::$app->user->identity;

                        return true;
                    },
                    'update' => function ($model) {
                        $user = Yii::$app->user->identity;
                        return true;
                        return $user->role == 1;
                    },
                ],
            ],
        ],
    ]);
    ?>


</div>
