<?php

namespace app\controllers;

use app\models\Record;
use app\models\Book;
use app\models\RecordHistory;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * RecordController implements the CRUD actions for Record model.
 */
class RecordController extends Controller {

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
     * Lists all Record models.
     *
     * @return string
     */
    public function actionIndex() {
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

        $bookId = intval($_GET['book']);
        $book = \app\models\Book::find()->andWhere(["id" => $bookId])->one();

        $record = new Record();
        $record->load($this->request->get());

        $query = Record::find()
                ->andWhere(['book_id' => $bookId])
                ->andWhere(['deleted' => 0]);
        if ($record->age) {
            //$query->andWhere("age like '%" . $record->age . "%'");
            $query->andWhere(["like", "age", $record->age]);
        }

        if ($record->fio) {
            //$query->andWhere("fio like '%" . $record->fio . "%'");
            $query->andWhere(["like", "fio", $record->fio]);
        }

        if ($record->numReg) {
            //$query->andWhere("numReg like '%" . $record->numReg . "%'");
            $query->andWhere(["like", "numReg", $record->numReg]);
        }

        if ($record->death_date) {
            //$query->andWhere("death_date like '%" . $record->death_date . "%'");
            $query->andWhere(["like", "death_date", $record->death_date]);
        }

        if ($record->rip_date) {
            //$query->andWhere("rip_date like '%" . $record->rip_date . "%'");
            $query->andWhere(["like", "rip_date", $record->rip_date]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
                    'user' => $user,
                    'model' => new Record(),
                    'book_id' => $bookId,
                    'book' => $book
        ]);
    }

    public function actionVopros() {
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

        //$bookId = intval($_GET['book']);
        //$book = \app\models\Book::find()->andWhere(["vopros" => 1])->one();

        $record = new Record();
        $record->load($this->request->get());

        $query = Record::find()->andWhere(['vopros' => 1]);
        if ($record->age) {
            //$query->andWhere("age like '%" . $record->age . "%'");
            $query->andWhere(["like", "age", $record->age]);
        }

        if ($record->fio) {
            //$query->andWhere("fio like '%" . $record->fio . "%'");
            $query->andWhere(["like", "fio", $record->fio]);
        }

        if ($record->numReg) {
            //$query->andWhere("numReg like '%" . $record->numReg . "%'");
            $query->andWhere(["like", "numReg", $record->numReg]);
        }

        if ($record->death_date) {
            //$query->andWhere("death_date like '%" . $record->death_date . "%'");
            $query->andWhere(["like", "death_date", $record->death_date]);
        }

        if ($record->rip_date) {
            //$query->andWhere("rip_date like '%" . $record->rip_date . "%'");
            $query->andWhere(["like", "rip_date", $record->rip_date]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('vopros', [
                    'dataProvider' => $dataProvider,
                    'user' => $user,
                    'model' => new Record(),
                        //'book_id' => $bookId,
                        // 'book' => $book
        ]);
    }

    public function actionDeleted() {
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

        //$bookId = intval($_GET['book']);
        //$book = \app\models\Book::find()->andWhere(["vopros" => 1])->one();

        $flash = '';
        if (isset($_GET['a'])) {
            switch ($_GET['a']) {
                case "restore":
                    $rc = Record::find()->andWhere(['id' => $_GET['record_id']])->one();
                    $rc->deleted = 0;
                    $rc->save();
                    \app\models\HelperLevoshkin::updateSearchRecord($rc);
                    $flash = "Запись была успешно восстановлена";
                    break;
                case "del":
                    $rc = Record::find()->andWhere(['id' => $_GET['record_id']])->one();
                    $rc->deleted = 2;
                    $rc->save();
                    $flash = "Запись помечена как окончательно удаленная";
                    break;
            }
        }

        $record = new Record();
        $record->load($this->request->get());

        $query = Record::find()->andWhere(['deleted' => 1]);
        if ($record->age) {
            //$query->andWhere("age like '%" . $record->age . "%'");
            $query->andWhere(["like", "age", $record->age]);
        }

        if ($record->fio) {
            //$query->andWhere("fio like '%" . $record->fio . "%'");
            $query->andWhere(["like", "fio", $record->fio]);
        }

        if ($record->numReg) {
            //$query->andWhere("numReg like '%" . $record->numReg . "%'");
            $query->andWhere(["like", "numReg", $record->numReg]);
        }

        if ($record->death_date) {
            //$query->andWhere("death_date like '%" . $record->death_date . "%'");
            $query->andWhere(["like", "death_date", $record->death_date]);
        }

        if ($record->rip_date) {
            //$query->andWhere("rip_date like '%" . $record->rip_date . "%'");
            $query->andWhere(["like", "rip_date", $record->rip_date]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 50
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ]
            ],
        ]);

        return $this->render('deleted', [
                    'dataProvider' => $dataProvider,
                    'user' => $user,
                    'model' => new Record(),
                    'flash' => $flash
                        //'book_id' => $bookId,
                        // 'book' => $book
        ]);
    }

    /**
     * Displays a single Record model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Record model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate($book_id) {
        $book = \app\models\Book::find()->andWhere(["id" => $book_id])->one();
        //print_r($book);exit;
        $model = new Record();

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->book_id = $book_id;
                $model->user_id = Yii::$app->user->id;
                $model->updated_at = time();

                if ($model->save()) {
                    \app\models\HelperLevoshkin::updateSearchRecord($model);
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    print_r($model);
                    exit;
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
                    'model' => $model,
                    'book' => $book
        ]);
    }

    /**
     * Updates an existing Record model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

        $model = $this->findModel($id);

        $next = Record::find()->andWhere("id > $id")->andWhere(['book_id' => $model->book_id])->orderBy("id")->one();
        $prev = Record::find()->andWhere("id < $id")->andWhere(['book_id' => $model->book_id])->orderBy("id desc")->one();
        $first = Record::find()->andWhere("user_id is null")->andWhere(['book_id' => $model->book_id])->orderBy("id")->one();

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->updated_at = time();
            $model->user_id = Yii::$app->user->id;
            $model->filename = strtr($model->filename, [
                "/" => "\\"
            ]);

            $model->vopros = 0;

            $model1 = $this->findModel($id);
            $rHistory = new RecordHistory();
            $rHistory->record_id = $model1->id;
            $rHistory->updated_at = time();
            $rHistory->user_id = Yii::$app->user->id;
            $rHistory->info = serialize($model1->attributes);
            $rHistory->save();
            if ($model->save()) {
                \app\models\HelperLevoshkin::updateSearchRecord($model);

                $id_next = $next ? $next->id : $model->id;

                $pnum = 1;
                if (($next) && ($model->filename == $next->filename)) {
                    $pnum = $_POST['pageNum'];
                }

                //return $this->redirect(['vopros']);
                //return $this->redirect(['update', 'id' => $id_next, "pnum" => $pnum]);
            }
        }

        return $this->render('update', [
                    'model' => $model,
                    'next' => $next,
                    'first' => $first,
                    'prev' => $prev,
                    'user' => $user
        ]);
    }

    public function actionExportCsv($id) {

        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="book_' . $id . '.csv"');
        //echo 111;exit;

        $csv = new \ParseCsv\Csv();
        //echo 1112233411111;exit;

        $csv->linefeed = "\n";

        $header = [
            'Номер записи',
            'ФИО',
            'Возраст',
            'Дата смерти',
            'Дата захоронения',
            'Номер документа ЗАГС',
            'ЗАГС',
            //'Землекоп',
            'Номер участка',
            'Номер ряда',
            'Номер могилы',
            'Родственники',
            'Файл',
            'Комментарий',
            'Захоронение',
        ];

        $header2 = [
            'NumReg',
            'Dead_FIO',
            'Age',
            'Death_Date',
            'RIP_Date',
            'DocNum',
            'ZAGS',
            //'Землекоп',
            'Area_Num',
            'Row_Num',
            'RIP_Num',
            'Relativ_FIO_Adress',
            'FileName',
            'Comment',
            'RIP_Style',
        ];

        $data_all = [];
        $data_all[] = $header2;
        $list = Record::find()->andWhere(['book_id' => $id])->all();
        foreach ($list as $elem) {
            $one = [];
            if ($elem->numReg) {
                $one[] = $elem->numReg;
            } else {
                $one[] = $elem->numLiteral;
            }

            $one[] = $elem->fio;
            $one[] = $elem->age;
            $one[] = $elem->death_date;
            $one[] = $elem->rip_date;
            $one[] = $elem->docnum;
            $one[] = $elem->zags;
            //$one[] = $elem->riper;
            $one[] = $elem->area_num;
            $one[] = $elem->row_num;
            $one[] = $elem->rip_num;
            $one[] = $elem->relative_fio;
            $one[] = $elem->filename;
            $one[] = $elem->comment;
            $one[] = $elem->rip_style == 1 ? "Гроб" : "Урна";
            $data_all[] = $one;
        }
        //$fp = fopen('out.csv', 'w');
        /*
          fputcsv($fp, $header, ',', '"', '');

          foreach ($data_all as $fields) {
          fputcsv($fp, $fields, ',', '"', '');
          }

          fclose($fp);
         */
        //echo 123;exit;
        $csv->output("book_$id.csv", $data_all, $header, ';');
        //      $csv->save();
        //echo 1111111;
        exit;
    }

    /**
     * Deletes an existing Record model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);

        $model->deleted = 1;
        $model->save();
        $book = Book::find()->andWhere(['id' => $model->book_id])->one();
        $c_id = $book->cemetery_id;
        $table_name = "__search_form_$c_id";

        $GLOBALS['search_form_table'] = $table_name;

        $sCache = \app\models\SearchFormBasic::find()->andWhere(['record_id' => $model->id])->one();
        if ($sCache) {
            $sCache->delete();
        }

        return $this->redirect(['index', 'book' => $book->id]);
    }

    /**
     * Finds the Record model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Record the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Record::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
