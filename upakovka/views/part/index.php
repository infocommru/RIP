<?php

use app\models\Part;
use app\models\Book;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use app\models\HelperLevoshkin;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Партии';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="part-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
            [
                'label' => 'Кладбише',
                'value' => function ($model) {
                    return $model->cemetery->name;
                }
            ],
            'number',
            [
                'label' => 'Пользователь',
                'value' => function ($model) {
                    if (!$model->user_id)
                        return "-";
                    return $model->user->username;
                }
            ],
            'records',
            [
                'label' => 'Кол-во книг',
                'value' => function ($model) {
                    return Book::find()->andWhere(['part_id' => $model->id])->count();
                }
            ],
            [
                'label' => 'Добавлено',
                'value' => function ($model) {
                    return date("Y-m-d H:i", $model->add_at);
                }
            ],
            [
                'label' => 'Статус',
                'value' => function ($model) {
                    return HelperLevoshkin::getPartStatuses()[$model->status];
                }
            ],
            [
                'label' => 'Результат проверки',
                'value' => function ($model) {
                    return HelperLevoshkin::getPartResultStatuses()[$model->status_result];
                }
            ],
            //'update_flag',
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
                        if ($model->user_id)
                            return false;
                        if (HelperLevoshkin::getUserPart())
                            return false;
                        //$user = Yii::$app->user->identity;

                        return true;
                    },
                ],
            ],
        ],
    ]);
    ?>


</div>
