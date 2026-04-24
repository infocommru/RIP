<?php

use app\models\RecordHistory;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = $model->book->name . ', запись №' . $model->numReg;

$user = \app\models\User::findIdentity(\Yii::$app->user->id);

if ($user->role == 1) {
    $this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/record/update', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = "История изменений";
} else {
    //$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['/record/update', 'id' => $model->id]];
    $this->params['breadcrumbs'][] = $this->title;
}

function td_table($data, $pole1, $pole2) {
    if ($pole1 == $pole2) {
        return "<td>$data</td>";
    } else {
        return "<td><span class='ne_ravno'>$data</span></td>";
    }
}
?>
<div class="record-history-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <hr />
    <?php
    //print_r(unserialize($history[0]->info));
    //exit;
    ?>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'label' => 'Обновлено',
                'value' => function ($model) {
                    if (!$model->updated_at)
                        return '-';
                    return date("Y-m-d H:i", $model->updated_at);
                }
            ],
            [
                'label' => 'Книга',
                'value' => function ($model) {
                    return $model->book->name;
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
            'riper',
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
        ],
    ])
    ?>
    <hr />
    <h4>Логи</h4>
    <?php if ($history): ?>
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Юзер</th>
                    <th>Изменено</th>
                    <th>Номер</th>
                    <th>ФИО</th>
                    <th>Возраст</th>
                    <th>Дата смерти</th>
                    <th>Дата захоронения</th>
                    <th>ЗАГС</th>
                    <th>Номер участка</th>
                    <th>Номер ряда</th>
                    <th>Номер могилы</th>
                    <th>Родственники</th>
                    <th>Файл</th>
                    <th>Комментарий</th>
                    <th>Захоронение</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $history_list = $history;
                for ($i = 0; $i < sizeof($history_list); $i++) {
                    $one = $history_list[$i];
                    $history_last = $model->attributes;
                    if (isset($history[$i + 1]))
                        $history_last = unserialize($history[$i + 1]->info);
                    //print_r($history_last);
                    //exit;
                    $info = unserialize($one->info);
                    $history = $info;
                    $num = $i + 1;
                    $login = $one->user->username;
                    $updated = date("Y-m-d H:i", $one->updated_at);

                    $regnum = $info['numReg'];
                    if (!$regnum)
                        $regnum = $info['numLiteral'];

                    $ripStyle = \app\models\Record::ripStyleTypes()[$info['rip_style']];

                    echo "<tr>";
                    echo "<td>$num</td>";
                    echo "<td>$login</td>";
                    echo "<td>$updated</td>";
                    //echo "<td>$regnum</td>";

                    echo td_table($regnum, $history_last['numReg'], $history['numReg']);
                    echo td_table($info['fio'], $history_last['fio'], $history['fio']);
                    echo td_table($info['age'], $history_last['age'], $history['age']);
                    echo td_table($info['death_date'], $history_last['death_date'], $history['death_date']);
                    echo td_table($info['rip_date'], $history_last['rip_date'], $history['rip_date']);
                    echo td_table($info['zags'], $history_last['zags'], $history['zags']);
                    echo td_table($info['area_num'], $history_last['area_num'], $history['area_num']);
                    echo td_table($info['row_num'], $history_last['row_num'], $history['row_num']);
                    echo td_table($info['rip_num'], $history_last['rip_num'], $history['rip_num']);
                    echo td_table($info['relative_fio'], $history_last['relative_fio'], $history['relative_fio']);
                    echo td_table($info['filename'], $history_last['filename'], $history['filename']);
                    echo td_table($info['comment'], $history_last['comment'], $history['comment']);
                    echo td_table($ripStyle, $history_last['rip_style'], $history['rip_style']);

                    //echo "<td>$info[fio]</td>";
                    //echo "<td>$info[age]</td>";
                    //echo "<td>$info[death_date]</td>";
                    //echo "<td>$info[rip_date]</td>";
                    //echo "<td>$info[zags]</td>";
                    //echo "<td>$info[area_num]</td>";
            //echo "<td>$info[row_num]</td>";
                    //echo "<td>$info[rip_num]</td>";
                    //echo "<td>$info[relative_fio]</td>";
                    //echo "<td>$info[filename]</td>";
                    //echo "<td>$info[comment]</td>";
                    //echo "<td>$ripStyle</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    <?php else: ?>
    <p>История изменений пуста</p>
    <?php endif; ?>


</div>
