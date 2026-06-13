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
        //$table_name = "__search_form_$c_id";
        //$GLOBALS['search_form_table'] = $table_name;
        \app\models\CacheRecords::$c_id = $c_id;

        if (empty($_GET)) {
            return false;
        }

        $query = \app\models\CacheRecords::find();
		$elasticQuery = [];

        if ($_GET['regnum']) {
            // phpinfo();exit;
            //print_r($_GET);
            //exit;
            $regnum = mb_strtolower($_GET['regnum']);
            
            switch ($_GET['rg_cont']) {
                case 1:
                	$condition = ['term' => ['regnum' => $regnum]];
                    break;
                case 2:
                	$condition = ['wildcard' => ['regnum.wildcard' => '*' . $regnum . '*']];
                    break;
                case 3:
                	$condition = ['prefix' => ['regnum' => $regnum]];
                    break;
                case 4:
                	$condition = ['wildcard' => ['regnum.wildcard' => '*' . $regnum]];
                    break;
            }
            
            $elasticQuery['bool']['must'][] = $condition;
        }

        if ($_GET['fam']) {
        	$fam = mb_strtolower($_GET['fam']);
        	
            switch ($_GET['fam_cont']) {
                case 1:
                	$condition = ['term' => ['fam' => $fam]];
                    break;
                case 2:
                	$condition = ['wildcard' => ['fam.wildcard' => '*' . $fam . '*']];
                    break;
                case 3:
                	$condition = ['prefix' => ['fam' => $fam]];
                    break;
                case 4:
                	$condition = ['wildcard' => ['fam.wildcard' => '*' . $fam]];
                    break;
            }
            $elasticQuery['bool']['must'][] = $condition;
        }

        if ($_GET['nam']) {
        	$nam = mb_strtolower($_GET['nam']);
        	
            switch ($_GET['nam_cont']) {
                case 1:
                	$condition = ['term' => ['nam' => $nam]];
                    break;
                case 2:
                	$condition = ['wildcard' => ['nam.wildcard' => '*' . $nam . '*']];
                    break;
                case 3:
                	$condition = ['prefix' => ['nam' => $nam]];
                    break;
                case 4:
                	$condition = ['wildcard' => ['nam.wildcard' => '*' . $nam]];
                    break;
            }
            $elasticQuery['bool']['must'][] = $condition;
        }


        if ($_GET['ot']) {
        	$ot = mb_strtolower($_GET['ot']);
        	
            switch ($_GET['ot_cont']) {
                case 1:
                	$condition = ['term' => ['ot' => $ot]];
                    break;
                case 2:
                	$condition = ['wildcard' => ['ot.wildcard' => '*' . $ot . '*']];
                    break;
                case 3:
                	$condition = ['prefix' => ['ot' => $ot]];
                    break;
                case 4:
                	$condition = ['wildcard' => ['ot.wildcard' => '*' . $ot]];
                    break;
            }
            $elasticQuery['bool']['must'][] = $condition;
        }

        if (isset($_GET['unknown'])) {
        	$elasticQuery['bool']['must'][] = ['term' => ['unknown' => 1]];
        }

        if ($_GET['unknown_number']) {
        	$elasticQuery['bool']['must'][] = ['wildcard' => ['unknown_number' => '*' . $_GET['unknown_number'] . '*']];
        }

        if ($_GET['age']) {
            $age = intval($_GET['age']);

            switch (intval($_GET['age_cmp'])) {
                case 3:
                    $condition = ['range' => ['age_int' => ['gt' => $age]]];
                    break;
                case 2:
                	$condition = ['range' => ['age_int' => ['lt' => $age]]];
                    break;
                default:
                	$condition = ['term' => ['age_int' => $age]];
            }
            $elasticQuery['bool']['must'][] = $condition;
        }

        if ($_GET['rip_style']) {
            $rStyle = intval($_GET['rip_style']);

            switch ($rStyle) {
                case 2:
                case 1:
                	$elasticQuery['bool']['must'][] = [
                	'bool' => [
		            	'should' => [
		            		[
		            			'bool' => [
		            				'must' => [
		            					['term' => ['rip_style' => $rStyle]],
		            			 		['term' => ['book_rip_style' => 0]]
		            			 	]
		            			 ]
		            		],
		            			['term' => ['book_rip_style' => $rStyle]]
		            		]
		            	]
		            ];
                    break;
                default:
                    break;
            }
        }

        if ($_GET['dead_y']) {
            $dead_year = intval($_GET['dead_y']);
            $dead_m = intval($_GET['dead_m']);
            $dead_d = intval($_GET['dead_d']);

            $dead_date = ($dead_d < 10 ? '0' . $dead_d : $dead_d) . '/' . ($dead_m < 10 ? '0' . $dead_m : $dead_m) . '/' . $dead_year;

            switch (intval($_GET['dead_year_cmp'])) {
                case 3:
                    $condition = ['range' => ['dead_date.date' => ['gt' => $dead_date]]];
                    break;
                case 2:
                    $condition = ['range' => ['dead_date.date' => ['lt' => $dead_date]]];
                    break;
                default:
                	$condition = ['bool' => ['must' => []]];
                	$condition['bool']['must'][] = ['term' => ['dead_year' => $dead_year]];
                	
                    if ($dead_m)
                        $condition['bool']['must'][] = ['term' => ['dead_month' => $dead_m]];
                    if ($dead_d)
                        $condition['bool']['must'][] = ['term' => ['dead_day' => $dead_d]];
            }
            
            $elasticQuery['bool']['must'][] = $condition;
        }

        if ($_GET['rip_y']) {
            $rip_year = intval($_GET['rip_y']);
            $rip_m = intval($_GET['rip_m']);
            $rip_d = intval($_GET['rip_d']);

            $rip_date = ($rip_d < 10 ? '0' . $rip_d : $rip_d) . '/' . ($rip_m < 10 ? '0' . $rip_m : $rip_m) . '/' . $rip_year;

            switch (intval($_GET['rip_year_cmp'])) {
                case 3:
                    $condition = ['range' => ['rip_date.date' => ['gt' => $rip_date]]];
                    break;
                case 2:
                    $condition = ['range' => ['rip_date.date' => ['lt' => $rip_date]]];
                    break;
                default:
                	$condition = ['bool' => ['must' => []]];
                    $condition['bool']['must'][] = ['term' => ['rip_year' => $rip_year]];
                    
                    if ($rip_m)
                        $condition['bool']['must'][] = ['term' => ['rip_month' => $rip_m]];
                    if ($rip_d)
                    	$condition['bool']['must'][] = ['term' => ['rip_day' => $rip_d]];
            }
            
            $elasticQuery['bool']['must'][] = $condition;
        }
        
        if ($_GET['zags']){  
            $elasticQuery['bool']['must'][] = ['bool' => ['should' => [
            		[
		        		'match' => ['zags' => [
		        			'query' => $_GET['zags'],
		        			'fuzziness' => 'auto',
		        			'boost' => 5]]
            		],
            		[
            		'match' => [
            			'zags.autocomplete' => $_GET['zags']]
            		]
            	]]];
        }
        
        if ($_GET['docnum']) {
        	$elasticQuery['bool']['must'][] = ['wildcard' => ['docnum' => '*' . $_GET['docnum'] . '*']];
        }
        
        if ($_GET['comment']) {
            $elasticQuery['bool']['must'][] = ['bool' => ['should' => [
            		[
		        		'match' => ['comment' => [
		        			'query' => $_GET['comment'],
		        			'fuzziness' => 'auto',
		        			'boost' => 5]]
            		],
            		[
            		'match' => [
            			'comment.autocomplete' => $_GET['comment']]
            		]
            	]]];
        }

        if (isset($_GET['ext_search'])) {
            if ($_GET['areanum']) {
                switch ($_GET['area_cont']) {
                    case 1:
		            	$condition = ['term' => ['areanum' => $_GET['areanum']]];
		                break;
		            case 2:
		            	$condition = ['wildcard' => ['areanum.wildcard' => '*' . $_GET['areanum'] . '*']];
		                break;
		            case 3:
		            	$condition = ['prefix' => ['areanum' => $_GET['areanum']]];
		                break;
		            case 4:
		            	$condition = ['wildcard' => ['areanum.wildcard' => '*' . $_GET['areanum']]];
		                break;
                }
                $elasticQuery['bool']['must'][] = $condition;
            }

            if ($_GET['rownum']) {
                switch ($_GET['row_cont']) {
                    case 1:
		            	$condition = ['term' => ['rownum' => $_GET['rownum']]];
		                break;
		            case 2:
		            	$condition = ['wildcard' => ['rownum.wildcard' => '*' . $_GET['rownum'] . '*']];
		                break;
		            case 3:
		            	$condition = ['prefix' => ['rownum' => $_GET['rownum']]];
		                break;
		            case 4:
		            	$condition = ['wildcard' => ['rownum.wildcard' => '*' . $_GET['rownum']]];
		                break;
                }
                $elasticQuery['bool']['must'][] = $condition;
            }

            if ($_GET['ripnum']) {
                switch ($_GET['rip_cont']) {
                    case 1:
		            	$condition = ['term' => ['ripnum' => $_GET['ripnum']]];
		                break;
		            case 2:
		            	$condition = ['wildcard' => ['ripnum.wildcard' => '*' . $_GET['ripnum'] . '*']];
		                break;
		            case 3:
		            	$condition = ['prefix' => ['ripnum' => $_GET['ripnum']]];
		                break;
		            case 4:
		            	$condition = ['wildcard' => ['ripnum.wildcard' => '*' . $_GET['ripnum']]];
		                break;
                }
                $elasticQuery['bool']['must'][] = $condition;
            }

            if ($_GET['rel']) {
            	$elasticQuery['bool']['must'][] = ['bool' => ['should' => [
            		[
		        		'match' => ['relative' => [
		        			'query' => $_GET['rel'],
		        			'fuzziness' => 'auto',
		        			'boost' => 5]]
            		],
            		[
            		'match' => [
            			'relative.autocomplete' => $_GET['rel']]
            		]
            	]]];
            }
        }

		if (!empty($elasticQuery['bool']['must'])) {
    		$query->query($elasticQuery);
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

        //$result = $query->orderBy($table_name . '.id')->offset($offset)->limit($this->searchLimit)->joinWith('record')->asArray()->all();
        
        $result = $query->orderBy(['_score' => SORT_DESC, 'record_id' => SORT_DESC])
        	->offset($offset)
			->limit($this->searchLimit)
			->asArray()
			->all();
			
        //print_r($result);
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

            $one[] = $elem['_source']['regnum'];
            $one[] = $elem['_source']['fio_display'];
            $one[] = $elem['_source']['age'];
            $one[] = $elem['_source']['dead_date'];
            $one[] = $elem['_source']['rip_date'];
            $one[] = $elem['_source']['docnum'];
            $one[] = $elem['_source']['zags'];
            $one[] = $elem['_source']['rip_style'] == 1 ? "Гроб" : "Урна";
            //$one[] = $elem->riper;
            $one[] = $elem['_source']['areanum'] ?? '';
            $one[] = $elem['_source']['rownum'] ?? '';
            $one[] = $elem['_source']['ripnum'] ?? '';
            $one[] = $elem['_source']['relative'] ?? '';

            $dopInfo = "св. {$elem['_source']['svazka_num']}, кн. {$elem['_source']['book_num']}, стр. {$elem['_source']['page_num']}, п/п: {$elem['_source']['page_punkt']}";
            //if ($elem['record']['comment'])
            //    $dopInfo .= "\n " . $elem['record']['comment'];
            if ($elem['_source']['comment'])
                $dopInfo .= "\n " . $elem['_source']['comment'];

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
