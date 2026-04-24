<?php

namespace app\controllers;

use app\models\Part;
use app\models\PartRecord;
use app\models\Record;
use app\models\PartUpload;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * PartController implements the CRUD actions for Part model.
 */
class PartController extends Controller {

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
     * Lists all Part models.
     *
     * @return string
     */
    public function actionIndex() {
        $dataProvider = new ActiveDataProvider([
            'query' => Part::find(),
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

        return $this->render('index', [
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionUpdateRecord($part_id, $record_id = 0) {
        //echo 123;
        //echo $record_id;
        //echo '.'.$part_id;

        $first = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere(['status' => 0])
                ->orderBy("id")
                ->one();

        if (!$record_id)
            return $this->redirect(['update-record', 'part_id' => $part_id, "record_id" => $first->record_id]);

        $partRecordCurrent = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere(['record_id' => $record_id])
                ->one();

        $current = $partRecordCurrent->record;

        $part = Part::find()->andWhere(['id' => $part_id])->one();

        $record = $current;

        $prev = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere("id < " . $partRecordCurrent->id)
                ->orderBy("id desc")
                ->one();

        if ($prev)
            $prev = $prev->record;

        $next = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere("id > " . $partRecordCurrent->id)
                ->orderBy("id")
                ->one();

        if ($next)
            $next = $next->record;

        $invalid_count = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere(['status' => 2])
                ->count();

        $valid_count = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere(['status' => 3])
                ->count();

        $vopros_count = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                ->andWhere(['status' => 4])
                ->count();

        $total_count = PartRecord::find()
                ->andWhere(['part_id' => $part_id])
                //->andWhere(['status' => 3])
                ->count();

        if (isset($_GET['status'])) {
            $partRecordCurrent->status = intval($_GET['status']);
            $partRecordCurrent->save();

            if (floor($invalid_count * 100.0 / $total_count) >= 5) {
                $part->status = 2;
                $part->status_result = 2;
                $part->save();
                return $this->redirect(
                                [
                                    'index',
                                //'part_id' => $part_id,
                                //"record_id" => $nId
                ]);
            }

            if ((!$next) && (!$prev)) {
                $part->status = 2;
                $part->status_result = 1;
                $part->save();
                return $this->redirect(
                                [
                                    'index',
                                //'part_id' => $part_id,
                                //"record_id" => $nId
                ]);
            }

            $nId = $current->id;
            if ($next)
                $nId = $next->id;
            return $this->redirect(
                            [
                                'update-record',
                                'part_id' => $part_id,
                                "record_id" => $nId
            ]);
        }


        return $this->render('update', [
                    'current' => $current,
                    'part_record' => $partRecordCurrent,
                    'part' => $part,
                    'record' => $record,
                    'first' => $first,
                    'prev' => $prev,
                    'next' => $next,
                    'invalid_count' => $invalid_count,
                    'valid_count' => $valid_count,
                    'vopros_count' => $vopros_count,
                    'total_count' => $total_count
                        ]
        );
    }

    /**
     * Displays a single Part model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id) {
        if (isset($_FILES['reupload'])) {
            if (substr_count($_FILES['reupload']['name'], ".zip")) {
                $upload = new PartUpload();
                $upload->part_id = $id;
                $upload->add_at = time();
                $upload->filename = $_FILES['reupload']['name'];

                if ($upload->save()) {
                    $destination_path = "./upload/part/" . $upload->id . '.zip';
                    $source = $_FILES['reupload']['tmp_name'];
                    //echo $source.';'.$destination_path;exit;
                    move_uploaded_file($_FILES['reupload']['tmp_name'], $destination_path);
                }
            }
        }


        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Part model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate() {
        $model = new Part();

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
     * Updates an existing Part model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id) {
        $user = \Yii::$app->user->identity;
        $model = $this->findModel($id);
        //if ($model->user_id)
        //    return $this->redirect(['index',]);

        $model->user_id = $user->id;
        $model->status = 1;
        $model->save();

        return $this->actionUpdateRecord($id);
        /*
          $model = $this->findModel($id);

          if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
          return $this->redirect(['view', 'id' => $model->id]);
          }

          return $this->render('update', [
          'model' => $model,
          ]);
         * 
         */
    }

    public function actionPartBookDownload($id) {
        header('Content-Disposition: attachment; filename="books_' . $id . '.txt"');
        header('Content-Type: application/octet-stream');
        $books = \app\models\Book::find()->andWhere(['part_id' => $id])->all();
        foreach ($books as $book) {
            echo $book->name . "\n";
        }
        //echo $id;
        exit;
    }

    public function actionExportCsv($id) {

        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename="part_' . $id . '.csv"');
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

        $bad_records = PartRecord::find()
                ->andWhere(['part_id' => $id])
                ->andWhere(['status' => 2])
                ->all();

        $bad_ids = [];
        foreach ($bad_records as $bad) {
            $bad_ids[] = $bad->record_id;
        }

        $list = Record::find()->andWhere(['in', "id", $bad_ids])->all();
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
        $csv->output("part_$id.txt", $data_all, $header, ';');
        //      $csv->save();
        //echo 1111111;
        exit;
    }

    /**
     * Deletes an existing Part model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Part model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Part the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Part::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function beforeAction($action) {
        //if ($action->id == 'my-method') {
        $this->enableCsrfValidation = false;
        //}
        return parent::beforeAction($action);
    }
}
