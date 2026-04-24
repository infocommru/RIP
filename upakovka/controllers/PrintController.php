<?php

namespace app\controllers;

use app\models\Book;
use app\models\Record;
use app\models\Cemetery;
use app\models\SearchFormBasic;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class PrintController extends Controller {

    /**
     * @inheritDoc
     */
    public function behaviors() {

        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                        'actions' => [
                            'delete' => ['POST'],
                        ],
                    ],
                    'access' => [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'allow' => false,
                                'roles' => ['?'],
                            ],
                            [
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
                    ],
                ]
        );
    }

    public function actionIndex($record_id) {
        $record = Record::find()
                ->andWhere(['id' => $record_id])
                ->one();

        $book = Book::find()
                ->andWhere(['id' => $record->book_id])
                ->one();

        $cemetery = Cemetery::find()
                ->andWhere(['id' => $book->cemetery_id])
                ->one();

        $table_name = "__search_form_" . $cemetery->id;
        $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

        $sdata = SearchFormBasic::find()->andWhere(['record_id' => $record_id])->one();
        $user = \app\models\User::findIdentity(\Yii::$app->user->id);
        return $this->render('index',
                        [
                            'record' => $record,
                            'book' => $book,
                            'sdata' => $sdata,
                            'user' => $user,
                            'cemetery' => $cemetery
                        ]
        );
    }

    public function actionForma() {
        $this->layout = false;
        if ($_GET['spravka'] == '1') {
            return $this->render('form11',);
        } else {
            return $this->render('form2',);
        }
    }

    public function actionF1($record_id) {
        $record = Record::find()
                ->andWhere(['id' => $record_id])
                ->one();

        $book = Book::find()
                ->andWhere(['id' => $record->book_id])
                ->one();

        $cemetery = Cemetery::find()
                ->andWhere(['id' => $book->cemetery_id])
                ->one();

        return $this->render('f1',
                        [
                            'record' => $record,
                            'book' => $book,
                            'cemetery' => $cemetery
                        ]
        );
    }
}
