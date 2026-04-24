<?php

namespace app\controllers;

use app\models\Record;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

/**
 * RecordController implements the CRUD actions for Record model.
 */
class SearchController extends Controller {

    public $searchLimit = 100;

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

    protected function searchCemetery($c_id) {
        $table_name = "__search_form_$c_id";
        $GLOBALS['search_form_table'] = $table_name;

        if (empty($_GET)) {
            return false;
        }

        $query = \app\models\SearchFormBasic::find();

        if ($_GET['regnum']) {
            // phpinfo();exit;
            //print_r($_GET);
            //exit;
            switch ($_GET['rg_cont']) {
                case 1:
                    $query->andWhere(['regnum' => $_GET['regnum']]);
                    break;
                case 2:
                    $query->andWhere(['like', 'regnum', $_GET['regnum']]);
                    break;
                case 3:
                    $query->andWhere(['like', 'regnum', $_GET['regnum'] . '%', false]);
                    break;
                case 4:
                    $query->andWhere(['like', 'regnum', '%' . $_GET['regnum'], false]);
                    break;
            }
        }

        if ($_GET['fam']) {
            switch ($_GET['fam_cont']) {
                case 1:
                    $query->andWhere(['fam' => $_GET['fam']]);
                    break;
                case 2:
                    $query->andWhere(['like', 'fam', $_GET['fam']]);
                    break;
                case 3:
                    $query->andWhere(['like', 'fam', $_GET['fam'] . '%', false]);
                    break;
                case 4:
                    $query->andWhere(['like', 'fam', '%' . $_GET['fam'], false]);
                    break;
            }
        }

        if ($_GET['nam']) {
            switch ($_GET['nam_cont']) {
                case 1:
                    $query->andWhere(['nam' => $_GET['nam']]);
                    break;
                case 2:
                    $query->andWhere(['like', 'nam', $_GET['nam']]);
                    break;
                case 3:
                    $query->andWhere(['like', 'nam', $_GET['nam'] . '%', false]);
                    break;
                case 4:
                    $query->andWhere(['like', 'nam', '%' . $_GET['nam'], false]);
                    break;
            }
        }


        if ($_GET['ot']) {
            switch ($_GET['ot_cont']) {
                case 1:
                    $query->andWhere(['ot' => $_GET['ot']]);
                    break;
                case 2:
                    $query->andWhere(['like', 'ot', $_GET['ot']]);
                    break;
                case 3:
                    $query->andWhere(['like', 'ot', $_GET['ot'] . '%', false]);
                    break;
                case 4:
                    $query->andWhere(['like', 'ot', '%' . $_GET['ot'], false]);
                    break;
            }
        }

        if (isset($_GET['unknown'])) {
            $query->andWhere(['unknown' => 1]);
        }

        if ($_GET['unknown_number']) {
            $query->andWhere(['like', 'unknown_number', $_GET['unknown_number']]);
        }

        if ($_GET['age']) {
            $age = intval($_GET['age']);

            switch (intval($_GET['age_cmp'])) {
                case 3:
                    $query->andWhere("$table_name.age > $age");
                    break;
                case 2:
                    $query->andWhere("$table_name.age < $age");
                    break;
                default:
                    $query->andWhere("$table_name.age = $age");
            }
        }

        if ($_GET['rip_style']) {
            $rStyle = intval($_GET['rip_style']);

            switch ($rStyle) {
                case 2:
                case 1:
                    $query->andWhere("(($table_name.rip_style = $rStyle)and($table_name.book_rip_style=0))or($table_name.book_rip_style=$rStyle)");
                    break;
                default:
                    break;
            }
        }

        if ($_GET['dead_y']) {
            $dead_year = intval($_GET['dead_y']);
            $dead_m = intval($_GET['dead_m']);
            $dead_d = intval($_GET['dead_d']);

            $dead_date = $dead_year . '' . ($dead_m < 10 ? '0' . $dead_m : $dead_m) . ($dead_d < 10 ? '0' . $dead_d : $dead_d);

            switch (intval($_GET['dead_year_cmp'])) {
                case 3:
                    $query->andWhere("dead_date > $dead_date");
                    break;
                case 2:
                    $query->andWhere("dead_date < $dead_date");
                    break;
                default:
                    $query->andWhere("dead_year = $dead_year");
                    if ($dead_m)
                        $query->andWhere("dead_month = $dead_m");
                    if ($dead_d)
                        $query->andWhere("dead_day = $dead_d");
            }
        }

        if ($_GET['rip_y']) {
            $rip_year = intval($_GET['rip_y']);
            $rip_m = intval($_GET['rip_m']);
            $rip_d = intval($_GET['rip_d']);

            $rip_date = $rip_year . '' . ($rip_m < 10 ? '0' . $rip_m : $rip_m) . ($rip_d < 10 ? '0' . $rip_d : $rip_d);

            switch (intval($_GET['rip_year_cmp'])) {
                case 3:
                    $query->andWhere("$table_name.rip_date > $rip_date");
                    break;
                case 2:
                    $query->andWhere("$table_name.rip_date < $rip_date");
                    break;
                default:
                    $query->andWhere("rip_year = $rip_year");
                    if ($rip_m)
                        $query->andWhere("rip_month = $rip_m");
                    if ($rip_d)
                        $query->andWhere("rip_day = $rip_d");
            }
        }
        
        if ($_GET['zags']){
        	switch ($_GET['zags_cont']) {
                case 1:
                    $query->andWhere(["$table_name.zags" => $_GET['zags']]);
                    break;
                case 2:
                    $query->andWhere(['like', "$table_name.zags", $_GET['zags']]);
                    break;
                case 3:
                    $query->andWhere(['like', "$table_name.zags", $_GET['zags'] . '%', false]);
                    break;
                case 4:
                    $query->andWhere(['like', "$table_name.zags", '%' . $_GET['zags'], false]);
                    break;
            }
            //$query->andWhere(["$table_name.zags" => $_GET['zags']]);
        }
        
        if ($_GET['docnum']) {
        	$query->andWhere(['like', $table_name . '.docnum', $_GET['docnum']]);
        }
        
        if ($_GET['comment']) {
        	$query->andWhere(['like', $table_name . '.comment', $_GET['comment']]);
        }

        if (isset($_GET['ext_search'])) {
            if ($_GET['areanum']) {
                switch ($_GET['area_cont']) {
                    case 1:
                        $query->andWhere(['areanum' => $_GET['areanum']]);
                        break;
                    case 2:
                        $query->andWhere(['like', 'areanum', $_GET['areanum']]);
                        break;
                    case 3:
                        $query->andWhere(['like', 'areanum', $_GET['areanum'] . '%', false]);
                        break;
                    case 4:
                        $query->andWhere(['like', 'areanum', '%' . $_GET['areanum'], false]);
                        break;
                }
            }

            if ($_GET['rownum']) {
                switch ($_GET['row_cont']) {
                    case 1:
                        $query->andWhere(['rownum' => $_GET['rownum']]);
                        break;
                    case 2:
                        $query->andWhere(['like', 'rownum', $_GET['rownum']]);
                        break;
                    case 3:
                        $query->andWhere(['like', 'rownum', $_GET['rownum'] . '%', false]);
                        break;
                    case 4:
                        $query->andWhere(['like', 'rownum', '%' . $_GET['rownum'], false]);
                        break;
                }
            }

            if ($_GET['ripnum']) {
                switch ($_GET['rip_cont']) {
                    case 1:
                        $query->andWhere(['ripnum' => $_GET['ripnum']]);
                        break;
                    case 2:
                        $query->andWhere(['like', 'ripnum', $_GET['ripnum']]);
                        break;
                    case 3:
                        $query->andWhere(['like', 'ripnum', $_GET['ripnum'] . '%', false]);
                        break;
                    case 4:
                        $query->andWhere(['like', 'ripnum', '%' . $_GET['ripnum'], false]);
                        break;
                }
            }

            if ($_GET['rel']) {
                $query->andWhere(['like', 'relative', $_GET['rel']]);
            }
        }

        $count = $query->count();

        $curpage = 1;
        if (isset($_GET['pager'])) {
            $pages = explode(';', $_GET['pager']);
            $curpage = 1;
            foreach ($pages as $p) {
                $pp = explode(",", $p);
                if ($pp[0] == $c_id)
                    $curpage = $pp[1];
            }
        }

        $offset = ($curpage - 1) * $this->searchLimit;

        $result = $query->orderBy($table_name . '.id')->offset($offset)->limit($this->searchLimit)->joinWith('record')->asArray()->all();
        //print_r($result);exit;
        return [$result, $count];
    }

    public function actionIndex() {

        $search_data = false;
        if (isset($_GET['fam'])) {
            $search_data = [];
            $cemeteries = \app\models\Cemetery::find()
                    ->orderBy("name");

            if ($_GET['cemetery'] != '0') {
                $cemeteries->andWhere(['id' => $_GET['cemetery']]);
            }

            $cemeteries = $cemeteries->all();

            foreach ($cemeteries as $cemetery) {
                $data = $this->searchCemetery($cemetery->id);
                $counter = $data[1];
                $data = $data[0];
                if ($data) {
                    $key = $cemetery->id . ',' . $cemetery->name . ',' . $counter;
                    $search_data[$key] = $data;
                }
            }
        }

        return $this->render('index', [
                    'search_data' => $search_data
        ]);
    }

    public function actionExport($c_id) {
        $cemetery = \app\models\Cemetery::find()->andWhere(['id' => $c_id])->one();
        if (isset($_GET['pager'])) {
            unset($_GET['pager']);
        }
        $this->searchLimit = 10000;
        $data = $this->searchCemetery($cemetery->id);

        //print_r($data);
        //exit;
        //header('Content-type: application/octet-stream');
        //header('Content-Disposition: attachment; filename="search.csv"');

        $csv = new \ParseCsv\Csv();
        $csv->linefeed = "\n";

        $header = [
            'Номер записи',
            'ФИО',
            'Возраст',
            'Дата смерти',
            'Дата захоронения',
            'Документ',
            'ЗАГС',
            'Захоронение',
            //'Землекоп',
            'Номер участка',
            'Номер ряда',
            'Номер могилы',
            'Родственники',
            'Доп. инфо',
        ];

        $data_all = [];

        foreach ($data[0] as $elem) {
            $one = [];

            $one[] = $elem['regnum'];
            $one[] = $elem['record']['fio'];
            $one[] = $elem['record']['age'];
            $one[] = $elem['record']['death_date'];
            $one[] = $elem['record']['rip_date'];
            $one[] = $elem['docnum'];
            $one[] = $elem['record']['zags'];
            $one[] = $elem['rip_style'] == 1 ? "Гроб" : "Урна";
            //$one[] = $elem->riper;
            $one[] = $elem['record']['area_num'];
            $one[] = $elem['record']['row_num'];
            $one[] = $elem['record']['rip_num'];
            $one[] = $elem['relative'];

            $dopInfo = "св. $elem[svazka_num], кн. $elem[book_num], стр. $elem[page_num], п/п: $elem[page_punkt]";
            if ($elem['record']['comment'])
                $dopInfo .= "\n " . $elem['record']['comment'];

            $one[] = $dopInfo;
            $data_all[] = $one;
        }
        //$csv->save("./temp/search.csv", $data_all, $header, ';');
        @unlink("./temp/search.csv");
        @unlink("./temp/search.xlsx");
        if (isset($_GET['csv'])) {
            $out = $csv->output("search.csv", $data_all, $header, ';');
            exit;
        }
        $out = $csv->unparse($data_all, $header, null, null, ';');

        $user = \app\models\User::findIdentity(Yii::$app->user->id);
        $userId = $user->id;
        file_put_contents("./temp/search.csv", $out);
        if (file_exists("C:/python311/python.exe")) {
            //@exec("python3 ./temp/to_excel.py");   
            //phpinfo();
            //exit;
            exec("C:/python311/python -u ./temp/to_excel.py");
            exit;
        } else {
            //echo '!';
            exec("python3 -u ./temp/to_excel.py 2>&1", $r);
            //print_r($r);
            //exit;
        }

        header("Location: /web/temp/search.xlsx");

        exit;
        //$csv->save($dopInfo);
        //exit;
    }

    public function actionVopros($record_id) {
        $record = Record::find()->andWhere(['id' => $record_id])->one();
        $record->vopros = 1;
        $record->save();
        echo $record->fio;
    }

    public function actionBookCover($record_id) {
        $record1 = Record::find()->andWhere(['id' => $record_id])->orderBy('id')->one();
        $filepath = str_replace("\\", "/", $record1->filename);
        $index_last = strrpos($filepath, "/");
        $folderpath = substr($filepath, 0, $index_last);
        $fname = substr($filepath, $index_last + 1);

        $fullpath = "../upload/rip2/$folderpath";

        $files = glob($fullpath . "/*.*");

        $file0 = strtr($files[0], ["../upload" => "/upload"]);
        header("Location: $file0");
        exit;
    }
}
