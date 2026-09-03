<?php

use app\models\Helper;
use app\models\User;
use app\models\Record;
use yii\helpers\Url;

use yii\grid\GridView;
use yii\widgets\Pjax;

$user = app\models\User::findIdentity(Yii::$app->user->id);
$tabId = $tabId ?? Yii::$app->request->get('id', 'default');
?>

<h5>Всего записей: <?= $count_result ?>. Выгрузить <a href="<?= "&c_id=" ?>">excel</a></h5>

<?php Pjax::begin([
    'id' => 'pjax-tab-' . $tabId,
    'enablePushState' => false, // Чтобы клик по странице не переписывал URL браузера
    'timeout' => 5000,
]); ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'fio_display:text:ФИО',
        'age:text:Год рождения/Возраст',
        'dead_date:text:Дата смерти',
        'rip_date:text:Дата захоронения',
    ],
    'pager' => [
        'class' => \yii\widgets\LinkPager::class,
    ],
]); ?>

<?php Pjax::end(); ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {

    });
</script>