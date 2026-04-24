<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Book;
use app\models\Part;
use app\models\PartRecord;
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

    <?=
    DetailView::widget([
        'model' => $user,
        'attributes' => [
            'username',
            'firstname',
            'lastname',
            [
                'label' => 'Обработка кладбища',
                'value' => function ($model) {
                    $user = \Yii::$app->user->identity;

                    $part = Part::find()
                            ->andWhere(['user_id' => $user->id])
                            ->andWhere(['status' => 1])
                            ->one();

                    if (!$part)
                        return '-';
                    return $part->cemetery->name;
                }
            ],
            [
                'label' => 'Обработка партии',
                'value' => function ($model) {
                    $user = \Yii::$app->user->identity;

                    $part = Part::find()
                            ->andWhere(['user_id' => $user->id])
                            ->andWhere(['status' => 1])
                            ->one();

                    if (!$part)
                        return '-';

                    return '#' . $part->id;
                }
            ],
            [
                'label' => 'Записи',
                'value' => function ($model) {
                    $user = \Yii::$app->user->identity;

                    $part = Part::find()
                            ->andWhere(['user_id' => $user->id])
                            ->andWhere(['status' => 1])
                            ->one();

                    if (!$part)
                        return '-';

                    $total_count = PartRecord::find()
                            ->andWhere(['part_id' => $part->id])
                            //->andWhere(['status' => 3])
                            ->count();

                    $vopros_count = PartRecord::find()
                            ->andWhere(['part_id' => $part->id])
                            ->andWhere('status > 0')
                            ->count();

                    $progress = 100.0 * $vopros_count / $total_count;

                    return $vopros_count . " [" . number_format($progress, 2, ".", ".") . "%] | $total_count";
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
        ],
    ])
    ?>

</div>
