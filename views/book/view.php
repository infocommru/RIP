<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Record;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
$user = \app\models\User::findIdentity(\Yii::$app->user->id);
$is_admin = $user->role == 1;

$this->title = "Книга #" . $model->name . " (" . ($model->cemetery->name) . ")";
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$record1 = Record::find()->andWhere(['book_id' => $model->id])->orderBy('id')->one();

$filepath = str_replace("\\", "/", $record1->filename);
$index_last = strrpos($filepath, "/");
$folderpath = substr($filepath, 0, $index_last);
$fname = substr($filepath, $index_last + 1);

$fullpath = "../upload/rip2/$folderpath";

$files = glob($fullpath . "/*.*");
$file0 = '';
if ($files)
    $file0 = strtr($files[0], ["../upload" => "/upload"]);

//print_r($files);exit;


$fname = strtr($record1->filename, [
    '002.' => "001.",
    '003.' => "001.",
    '004.' => "001.",
    '005.' => "001.",
    '006.' => "001.",
    '007.' => "001.",
    '008.' => "001.",
    '009.' => "009.",
        ]);

$obloshka = "$file0";
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($is_admin) echo Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?php echo Html::a('Смотреть записи', "/web/record?book=" . $model->id, ['class' => 'btn btn-primary']); ?>
        <?php if ($obloshka): ?>
            <a target="_blank" class='btn btn-primary' href='<?= $obloshka ?>'>Обложка</a>
        <?php endif; ?>
        <?php
        if ($is_admin) {
            echo Html::a('Удалить', ['Удалить', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Точно удалить?',
                    'method' => 'post',
                ],
            ]);
        }
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
            'comment',
            [
                'label' => 'Захоронение',
                'value' => function ($model) {
                    return \app\models\Book::ripStyleTypes()[$model->rip_style];
                }
            ],
        ],
    ])
    ?>
    <?php if ($is_admin): ?>
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
    <?php endif; ?>
</div>
