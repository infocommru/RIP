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

            $csv = new \ParseCsv\Csv();
            $csv->delimiter = ",";

            $base_path = $_FILES['csv']['tmp_name'];
            @exec("rm -f ./temp/out.csv");
            if (substr_count($_FILES['csv']['name'], ".xls")) {
                (exec("python3 ./temp/excel.py " . $base_path));
                $base_path = "./temp/out.csv";
            }

            \app\models\HelperCsv::processBookCsv($id, $base_path);

            if (isset($_POST['create_part'])) {
                HelperLevoshkin::setBookPart($book);
                HelperLevoshkin::setPartRecords();
            }
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

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            \app\models\HelperCache::updateCache([$model]);
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

        \app\models\HelperCache::deleteBook($book->id);
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
