<?php

use app\models\Record;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = "Записи, которые были удалены";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="record-index">
    <?php if ($flash): ?>
        <div class="alert alert-success" role="alert">
            <?= $flash ?>
        </div>
    <?php endif; ?>

    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (0): ?>
        <p>
            <?= Html::a('Добавить запись', ['create', 'book_id' => $book_id], ['class' => 'btn btn-success']) ?>
            <?= Html::a('Экспорт CSV', "/web/record/export-csv?id=" . $book_id, ['class' => 'btn btn-warning']) ?>
            <?= Html::a('Сбросить фильтры', "/web/record/index?book=" . $book_id, ['class' => 'btn btn-info']) ?>
        </p>
        `<?php endif; ?>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $model,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            'id',
            //'book_id',
            [
                'label' => 'Книга',
                'value' => function ($model) {
                    return $model->book->name;
                }
            ],
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
                'label' => ' ',
                'format' => 'html',
                'value' => function ($model) {
                    $suffix = "?record_id=" . $model->id . "&a=";
                    $html = "<a target='_blank' title='редактировать' href='/web/record/update?id=" . $model->id . "'> <img width='32px' src='/img/edit.png' /> </a>";
                    $html .= "<a title='восстановить'   href='/web/record/deleted$suffix" . 'restore' . "'> <img width='32px' src='/img/restore.png' /> </a>";
                    $html .= "<a title='удалить'   href='/web/record/deleted$suffix" . 'del' . "'> <img width='32px' src='/img/del.png' /> </a>";

                    return $html;
                }
            ],
        /*
          [
          'class' => ActionColumn::className(),
          'urlCreator' => function ($action, Record $model, $key, $index, $column) {
          return Url::toRoute([$action, 'id' => $model->id]);
          }
          ],
         * 
         */
        ],
    ]);
    ?>


</div>
