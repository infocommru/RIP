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
    /*
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
     */
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

    public function actionNotFoundF2() {
        $user = \app\models\User::findIdentity(\Yii::$app->user->id);

        $params = [];
        if (isset($_GET['params'])) {
            //echo base64_decode($_GET['params']);
            //exit;
            $params = @unserialize(base64_decode($_GET['params']));
        }

        return $this->render('notfoundf2',
                        [
                            'user' => $user,
                            'params' => $params
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

    public function actionFormaPdf() {
        (exec("python3 -u ./temp/printer.py \"" . $_SERVER['REQUEST_URI'] . "\""));
        (exec("python3 -u ./temp/pdfjpeg.py  "));

        $fname = "forma_f" . $_GET['spravka'] . '_N_' . $_GET['nn'] . date("_d_m_Y");

        switch ($_GET['saveas']) {
            case '1':
                header('Location: /web/temp/pdf_form.pdf');
                exit;
            case '2':
                header('Location: /web/temp/pdf.jpg');
                exit;
            case '4':
                $fileName = $fname . '.jpg';
                header('Content-Type: application/jpeg');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                $dat = file_get_contents('./temp/pdf.jpg');
                echo $dat;
                exit;
            case '3':
                $fileName = $fname . '.pdf';
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');
                header('Content-Transfer-Encoding: binary');
                $dat = file_get_contents('./temp/pdf_form.pdf');
                echo $dat;
                exit;
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
