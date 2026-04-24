<?php

namespace app\controllers;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

class OperatorController extends Controller {

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex() {
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

        return $this->render('index', [
                    'user' => $user,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionHelp() {
        return $this->render('help');
    }

    public function actionBook() {
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

        $bookId = 0;
        if (isset($_GET['book_id']))
            $bookId = @intval($_GET['book_id']);
        
        if ($bookId) {
            $book = Book::find()->andWhere(['id' => $bookId])->one();
            if ($book->status == 1) {
                $book->status = 2;
                $book->save();
                $user->book_id = $bookId;
                $user->save();
            }
        }

        $books = false;
        if ($user->cemetery) {
            $books = Book::find()
                    ->andWhere(["cemetery_id" => $user->cemetery_id])
                    ->all();
        };

        return $this->render('book', [
                    'user' => $user,
                    'books' => $books
        ]);
    }
}
