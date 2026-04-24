<?php

use app\models\Book;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$user = \app\models\User::findIdentity(\Yii::$app->user->id);
$is_admin = $user->role == 1;

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($is_admin) echo Html::a('Добавить книгу', ['create'], ['class' => 'btn btn-success']); ?>
        <?= Html::a('Сбросить фильтры', "/web/book/index", ['class' => 'btn btn-info']) ?>
    </p>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Кладбише',
                'value' => function ($model) {
                    return $model->cemetery->name;
                }
            ],
            'name',
            'number',
            'svazka',
            'year1',
            'year2',
            'records',
            [
                'class' => 'yii\grid\ActionColumn',
                //'contentOptions' => 
                'header' => '',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'delete' => function ($model) {
                        $user = Yii::$app->user->identity;
                        return $user->role == 1;
                    },
                    'view' => function ($model) {
                        //if($model->user_id)return false;
                        //$user = Yii::$app->user->identity;

                        return true;
                    },
                    'update' => function ($model) {
                        $user = Yii::$app->user->identity;
                        return $user->role == 1;
                    },
                ],
            ],
        ],
    ]);
    ?>


</div>
