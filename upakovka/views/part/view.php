<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;
use app\models\Book;
use app\models\Record;
use app\models\HelperLevoshkin;
use app\models\PartUpload;

/** @var yii\web\View $this */
/** @var app\models\Part $model */
$title = $model->cemetery->name . ', Партия № ' . $model->number;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Партии', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="part-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if ((!$model->user_id) && (!HelperLevoshkin::getUserPart()))
            echo
            Html::a('В работу', ['update', 'id' => $model->id], ['class' => 'btn btn-primary'])
            ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
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
                    if ($model->user_id)
                        return $model->user->username;
                    return '-';
                }
            ],
            [
                'label' => 'Кол-во книг',
                'value' => function ($model) {
                    return Book::find()
                            ->andWhere(['part_id' => $model->id])
                            ->count();
                }
            ],
            'records',
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
        ],
    ])
    ?>
    <hr />

    <h4>Книги <a href="/web/part/part-book-download?id=<?= $model->id ?>">скачать</a></h4>
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Кол-во записей</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $books = Book::find()->andWhere(['part_id' => $model->id])->all();
            $num = 1;
            foreach ($books as $book) {
                echo "<tr>";
                echo "<td>$num</td>";
                echo "<td>" . $book->name . "</td>";
                echo "<td>" . Record::find()->andWhere(['book_id' => $book->id])->count() . "</td>";

                echo "</tr>";
                $num++;
            }
            ?>

        </tbody>
    </table>
    <?php if (\app\models\PartRecord::find()->andWhere(['part_id' => $model->id])->andWhere(['in', 'status', [2]])->count()): ?>
        <a class="btn btn-danger" href="/web/part/export-csv?id=<?= $model->id ?>">экспорт плохих записей</a>
    <?php endif; ?>
    <?php
    $uploads = PartUpload::find()
            ->andWhere(['part_id' => $model->id])
            ->orderBy("id desc")
            ->limit(10)
            ->all();
    ?>


    <h4>Логи загрузки</h4>
    <?php if ($uploads): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Добавлено</th>
                    <th>Файл</th>
                    <th>Статус</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($uploads as $upload) {
                    echo "<tr>";
                    echo "<td>" . $upload->id . "</td>";
                    echo "<td>" . date("Y-m-d H:i", $upload->add_at) . "</td>";

                    echo "<td>" . $upload->filename . "</td>";
                    echo "<td>" . HelperLevoshkin::getPartUploadStatuses()[$upload->status] . "</td>";

                    echo "</tr>";
                }
                ?>

            </tbody>
        </table>
    <?php else: ?>
        <p>В данную партию не было загрузок</p>

    <?php endif; ?>
    <form enctype="multipart/form-data" method='post'>
        <label for='reupload'>Файл:</label>
        <input type=file name=reupload id='reupload'>
        <input type=submit value="вгрузить">
    </form>

    <?php
//print_r($_FILES);
    ?>

</div>
