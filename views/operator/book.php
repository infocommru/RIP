<?php

use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */
$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if (!$user->cemetery): ?>
    <h3>Админ не задал кладбище</h3>
<?php else: ?>
    <h3><?php echo $user->cemetery->name ?></h3>
    <hr />
    <?php if ($user->book): ?>
        <h4>Книга в работе:  <?php echo $user->book->name ?></h4>
    <?php endif; ?>

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Название</th>
                <th>Статус</th>
                <th></th>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            for ($i = 0; $i < sizeof($books); $i++) {
                $num = $i + 1;
                $book = $books[$i];
                $status_text = app\models\Book::getStatuses()[$book->status];

                $action = "";
                if ($book->status == 1) {
                    $action = "<a href='/web/operator/book?book_id=" . $book->id . "' class='btn btn-success'>в работу</a>";
                }

                echo "<tr>";
                echo "<td>$num</td>";
                echo "<td>" . $book->name . "</td>";
                echo "<td>$status_text</td>";
                echo "<td>$action</td>";
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
<?php endif; ?>