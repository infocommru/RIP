<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\User;

/** @var yii\web\View $this */
/** @var app\models\User $model */
$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if($model->book_id):?>
        <?=
        Html::a('Снять назначенную книгу', ['view', 'id' => $model->id, "delbook" => '1'], [
            'class' => 'btn btn-warning',
        /* 'data' => [
          'confirm' => 'Удаляем?',
          'method' => 'post',
          ],
         */        ])
        ?>
    <?php endif;?>
        <?=
        Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Удаляем?',
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
            'username',
            'password',
            'firstname',
            'lastname',
            'middlename',
            'position',
            [
                'label' => 'Роль',
                'value' => function ($model) {
                    return User::roleList()[$model->role];
                }
            ],
            /*
            [
                'label' => 'Кладбище',
                'value' => function ($model) {
                    if (!$model->cemetery)
                        return false;
                    return $model->cemetery->name;
                }
            ],
            [
                'label' => 'Книга',
                'attribute' => 'html',
                'value' => function ($model) {
                    if (!$model->book)
                        return false;
                    $result = $model->book->name;
                    //$result .= "<a href='/' class='btn btn-danger'>снять книгу</a>";
                    return $result;
                }
            ],
            */
        ],
    ])
    ?>

</div>
