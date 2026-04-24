<?php

use yii\widgets\ListView;
?>
<h2>Кладбища</h2>

<?php
echo ListView::widget([
    'dataProvider' => $dataProvider,
    'options' => [
        'tag' => 'div',
        'class' => 'list-wrapper',
        'id' => 'list-wrapper',
    ],
    'pager' => [
        'options' => [
            'class' => 'pagination pagination-sm',
        ],
    ],
    'itemView' => 'parts/_cemetery_item',
]);
?>