<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Cemetery $model */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Кладбища', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cemetery-view">

    <?php if (!empty($_FILES)): ?>
        <div class="alert alert-success" role="alert">
            Подгрузили архив книг
        </div>
    <?php endif; ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?=
        Html::a('Удалить', ['delete', 'id' => $model->id], [
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
            'name:ntext',
            'description:ntext',
        ],
    ])
    ?>
    <hr />

    <h3>Вгрузить архив книг (.zip)</h3>
    <form enctype="multipart/form-data" method="post">
        <input name="zipfile" type="file"> 
        <input type="hidden" name="_csrf" value="<?= Yii::$app->request->getCsrfToken() ?>" />
        <input type="checkbox" id="create_part" name="create_part" >
        <label for="create_part">Партия на проверку</label>
        <input type="submit" value="Отправить">


    </form>

</div>
