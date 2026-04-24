<?php

namespace app\controllers;

use app\models\Book;
use app\models\HelperLevoshkin;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class BookController extends Controller {

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

    /**
     * Lists all Book models.
     *
     * @return string
     */
    public function actionIndex() {

        $book = new Book();
        $book->load($this->request->get());

        $query = Book::find()->andWhere(['deleted' => 0]);
        if ($book->name) {
//$query->andWhere("name like '%" . $book->name . "%'");
            $query->andWhere(["like", "name", $book->name]);
        }

        if ($book->number) {
//$query->andWhere("number like '%" . $book->number . "%'");
            $query->andWhere(["number" => $book->number]);
        }

        if ($book->svazka) {
//$query->andWhere("svazka like '%" . $book->svazka . "%'");
            $query->andWhere(["svazka" => $book->svazka]);
        }

        if ($book->records) {
//$query->andWhere("records like '%" . $book->records . "%'");
            $query->andWhere(["like", "records", $book->records]);
        }

        if ($book->year1) {
//$query->andWhere("year1 like '%" . $book->year1 . "%'");
            $query->andWhere(["like", "year1", $book->year1]);
        }

        if ($book->year2) {
//$query->andWhere("year2 like '%" . $book->year2 . "%'");
            $query->andWhere(["like", "year2", $book->year2]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
                /*
                  'pagination' => [
                  'pageSize' => 50
                  ],
                  'sort' => [
                  'defaultOrder' => [
                  'id' => SORT_DESC,
                  ]
                  ],
                 */
        ]);

        //$book = new Book();

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'model' => $book
        ]);
    }

    /**
     * Displays a single Book model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        $book = $this->findModel($id);
        if (!empty($_FILES)) {
            set_time_limit(0);
//print_r($_FILES);
//exit;
            $csv = new \ParseCsv\Csv();
//$csv->offset = 1;
            $csv->delimiter = ",";

// print_r($_FILES);exit;

            $base_path = $_FILES['csv']['tmp_name'];
            @exec("rm -f ./temp/out.csv");
            if (substr_count($_FILES['csv']['name'], ".xls")) {
                (exec("python3 ./temp/excel.py " . $base_path));
                $base_path = "./temp/out.csv";
            }

            //print_r($_POST);
            //exit;

            \app\models\HelperCsv::processBookCsv($id, $base_path);

            if (isset($_POST['create_part'])) {
                HelperLevoshkin::setBookPart($book);
                HelperLevoshkin::setPartRecords();
            }

            /*
              $csv->parseFile($base_path);

              $counter = 0;
              foreach ($csv->data as $row) {
              $query_last = \app\models\Record::find()->andWhere(['book_id' => $id]);
              $record = new \app\models\Record();
              $record->book_id = $id;
              $cnt = 0;
              $valid = true;
              foreach ($row as $k => $v) {
              switch ($cnt) {
              case 0:
              $vv = (intval($v)) . "";
              $query_last->andWhere(['book_id' => $record->book_id ]);
              if ($v == $vv) {
              $record->numReg = $v;
              $query_last->andWhere(['numReg' => $v]);
              } else {
              $record->numLiteral = $v;
              if ($v)
              $query_last->andWhere(['numLiteral' => $v]);
              }

              if (!$v) {
              $counter++;
              }

              if ($v == "NumReg") {
              $valid = false;
              }

              if ($query_last->one()) {
              if($v)
              $valid = false;
              }

              break;
              case 1:
              $record->fio = $v;
              break;
              case 2:
              $record->age = $v;
              break;
              case 3:
              $record->death_date = $v;
              break;
              case 4:
              $record->rip_date = $v;
              break;
              case 5:
              $record->docnum = $v;
              break;
              case 6:
              $record->zags = $v;
              break;
              case 7:
              $record->riper = $v;
              break;
              case 8:
              $record->area_num = $v;
              break;
              case 9:
              $record->row_num = $v;
              break;
              case 10:
              $record->rip_num = $v;
              break;
              case 11:
              $record->relative_fio = $v;
              break;
              case 15:
              $record->filename = $v;
              break;
              case 16:
              $record->comment = $v;
              break;
              case 17:
              if (($v == "Гроб") || ($v == "гроб")) {
              $record->rip_style = 1;
              } else {
              $record->rip_style = 2;
              }
              break;
              }
              $cnt++;
              }

              if ($valid)
              if (!$record->save()) {
              //print_r($record);
              }
              }

              //print_r($csv->data); */
        }

        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Book model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() {
        $model = new Book();

        if ($this->request->isPost) {
            if (isset($_POST['Book']['comment'])) {
                $model->comment = $_POST['Book']['comment'];
            }

            if (isset($_POST['Book']['rip_style'])) {
                $model->rip_style = $_POST['Book']['rip_style'];
            }

            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
                    'model' => $model,
        ]);
    }

    /**
     * Updates an existing Book model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if (isset($_POST['Book']['comment'])) {
            $model->comment = $_POST['Book']['comment'];
        }

        if (isset($_POST['Book']['rip_style'])) {
            $model->rip_style = $_POST['Book']['rip_style'];
        }

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            if ($model->rip_style) {
                //$cemetery = \app\models\Cemetery::find()
                //        ->andWhere([])
                //        ->one();

                $GLOBALS['search_form_table'] = "__search_form_" . $model->cemetery_id;
                \app\models\SearchFormBasic::updateAll(['book_rip_style' => $model->rip_style], 'book_id = ' . $model->id);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
                    'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Book model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $book = $this->findModel($id);
        $book->deleted = 1;
        $book->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Book model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Book the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Book::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function beforeAction($action) {

        $user = \app\models\User::findIdentity(\Yii::$app->user->id);
        if ($user->role != 1) {
            //$this->redirect(['/']);
        }


        return parent::beforeAction($action);
    }
}
