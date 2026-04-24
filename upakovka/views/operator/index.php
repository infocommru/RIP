<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Record;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
$this->title = $user->username;
$this->params['breadcrumbs'][] = ['label' => 'Профиль', 'url' => ['index']];
\yii\web\YiiAsset::register($this);

$model = $user;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (0): ?>
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
    <?php endif; ?>
    <?=
    DetailView::widget([
        'model' => $user,
        'attributes' => [
            'username',
            'firstname',
            'lastname',
            'middlename',
        /*
          [
          'label' => 'Обработка кладбища',
          'value' => function ($model) {
          if (!$model->cemetery)
          return '-';
          return $model->cemetery->name;
          }
          ],
          [
          'label' => 'Обработка книги',
          'value' => function ($model) {
          if (!$model->book)
          return '-';
          return $model->book->name;
          }
          ],
          [
          'label' => 'Записи',
          'value' => function ($model) {
          $total = Record::find()->andWhere(['book_id' => $model->book_id])->count();
          $updated = Record::find()
          ->andWhere(['book_id' => $model->book_id])
          ->andWhere("updated_at > 0")
          ->count();

          $progress = 0;
          if ($total)
          $progress = 100.0 * $updated / $total;
          return "$updated / $total | " . number_format($progress, 2, ".", ".") . '%';
          }
          ],
         */
        ],
    ])
    ?>

</div>
