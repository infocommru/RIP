<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
$this->title = "Книга #" . $model->name . " (" . ($model->cemetery->name) . ")";
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Смотреть записи', "/web/record?book=" . $model->id, ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Удалить', ['Удалить', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Точно удалить?',
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
            //'cemetery_id',
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
            'per_page',
            [
                'label' => 'Статус',
                'value' => function ($model) {
                    return Book::getStatuses()[$model->status];
                }
            ],
        ],
    ])
    ?>
    <div>
        <h4>Вгрузить записи</h4>
        <form enctype="multipart/form-data" method="post">
            <input name="csv" type="file"> 
            <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
            <input type="checkbox" id="create_part" name="create_part" >
            <label for="create_part">Партия на проверку</label>
            <input type="submit" value="отправить">


        </form>

    </div>
</div>
