<?php

namespace app\controllers;

use app\models\Record;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \avadim\FastExcelWriter\Excel;
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

    public function actionSearchSuggest($q, $variable)
    {
        $query = \app\models\CacheRecords::find();

        $elasticQuery = [
            'multi_match' => [
                'query' => $q,
                'type'  => 'bool_prefix',
                'fields' => [
                    $variable . '.autocomplete',
                    $variable . '.autocomplete._2gram',
                    $variable . '.autocomplete._3gram',
                ],
            ]
        ];

        $query->query($elasticQuery);
        $query->addCollapse(['field' => $variable . '.keyword']);

        $response = $query->orderBy(['_score' => SORT_DESC, 'record_id' => SORT_DESC])->all();
        $response = \yii\helpers\ArrayHelper::getColumn($response, $variable);

        return json_encode($response);
    }
    protected function searchTermConditions($search_string, $name_var) {
        $condition = ['bool' => ['should' => [
            [
                'match_phrase' => [
                    $name_var => [
                        'query' => $search_string,
                    ]
                ]
            ],
            [
                'term' => [
                    $name_var . '.keyword' => [
                        'value' => mb_strtolower($search_string),
                        'boost' => 10
                    ]
                ]
            ]
        ]]];

        return $condition;
    }
    protected function searchStartFromConditions($search_string, $name_var) {
        $condition = [
            'prefix' => [
                $name_var . '.keyword' => $search_string
            ]
        ];

        return $condition;
    }
    protected function searchEndFromConditions($search_string, $name_var) {
        $condition = [
            'wildcard' => [
                $name_var . '.wildcard' => [
                    'value' => '*' . addcslashes($search_string, '*?\\'),
                ],
            ]   
        ];

        return $condition;
    }
    protected function searchFuzzinessConditions($search_string, $name_var) {
        $condition = ['bool' => ['should' => [
            [
                'term' => [
                    $name_var . '.keyword' => [
                        'value' => mb_strtolower($search_string),
                        'boost' => 10
                    ]
                ]
            ],
            [
                'match_phrase' => [
                    $name_var => [
                        'query' => $search_string,
                        'boost' => 7
                    ]
                ]
            ],
            [
                'multi_match' => [
                    'query' => $search_string,
                    'type'  => 'bool_prefix',
                    'fields' => [
                        $name_var . '.autocomplete',
                        $name_var . '.autocomplete._2gram',
                        $name_var . '.autocomplete._3gram',
                    ],
                    'boost' => 5
                ]
            ],
            [
                'match' => [
                    $name_var => [
                        'query' => $search_string,
                        'fuzziness' => 'AUTO',
                        'boost' => 1
                    ]
                ]
            ],
        ],
        ]];
        return $condition;
    }

    protected function searchValue($switch ,$search_string, $name_var){
        switch ($switch) {
            case 1:
                $condition = self::searchTermConditions($search_string, $name_var);
                break;
            case 2:
                $condition = self::searchFuzzinessConditions($search_string, $name_var);
                break;
            case 3:
                $condition = self::searchStartFromConditions($search_string, $name_var);
                break;
            case 4:
                $condition = self::searchEndFromConditions($search_string, $name_var);
                break;
        }

        return $condition;
    }
    
    protected function searchCemetery($c_id) {

        if (empty($_GET)) {
            return false;
        }

        $query = \app\models\CacheRecords::find();
		$elasticQuery = [];
        $elasticQuery['bool']['must'][] = ['term' => ['cemetery_id' => $c_id]];

        if ($_GET['regnum']) {
            $condition = self::searchTermConditions($_GET['regnum'], 'regnum');
            $elasticQuery['bool']['must'][] = $condition;
        }

        if ($_GET['fam']) {
            $elasticQuery['bool']['must'][] = self::searchValue($_GET['fam_cont'], $_GET['fam'], 'fam');
        }

        if ($_GET['nam']) {
            $elasticQuery['bool']['must'][] = self::searchValue($_GET['nam_cont'], $_GET['nam'], 'nam');
        }

        if ($_GET['ot']) {
            $elasticQuery['bool']['must'][] = self::searchValue($_GET['ot_cont'], $_GET['ot'], 'ot');
        }

        if (isset($_GET['unknown'])) {
        	$elasticQuery['bool']['must'][] = ['term' => ['unknown' => 1]];
        }

        if ($_GET['unknown_number']) {
            $elasticQuery['bool']['must'][] = self::searchTermConditions( $_GET['unknown_number'], 'unknown_number');
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
            $elasticQuery['bool']['must'][] = self::searchValue($_GET['zags_cont'], $_GET['zags'], 'zags');
        }
        
        if ($_GET['docnum']) {
            $elasticQuery['bool']['must'][] = self::searchTermConditions($_GET['docnum'], 'docnum');
        }
        
        if ($_GET['comment']) {
            $elasticQuery['bool']['must'][] = self::searchTermConditions($_GET['comment'], 'comment');
        }

        if (isset($_GET['ext_search'])) {
            if ($_GET['areanum']) {
                $elasticQuery['bool']['must'][] = self::searchValue($_GET['area_cont'], $_GET['areanum'], 'areanum');
            }

            if ($_GET['rownum']) {
                $elasticQuery['bool']['must'][] = self::searchValue($_GET['row_cont'], $_GET['rownum'], 'rownum');
            }

            if ($_GET['ripnum']) {
                $elasticQuery['bool']['must'][] = self::searchValue($_GET['rip_cont'], $_GET['ripnum'], 'ripnum');
            }

            if ($_GET['rel']) {
            	$elasticQuery['bool']['must'][] = self::searchTermConditions($_GET['rel'], 'relative');
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

    private function exportData($c_id) {
        $cemetery = \app\models\Cemetery::find()->andWhere(['id' => $c_id])->one();

        if (isset($_GET['pager'])) {
            unset($_GET['pager']);
        }

        $this->searchLimit = 10000;
        $data = $this->searchCemetery($cemetery->id);
    
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

            if ($elem['_source']['comment'])
                $dopInfo .= "\n " . $elem['_source']['comment'];

            $one[] = $dopInfo;
            $data_all[] = $one;
        }

        return [$data_all, $header];
    }

    public function actionExport($c_id) {
        $data = $this->exportData($c_id);

        if (isset($_GET['csv'])) {
            $csv = new \ParseCsv\Csv();
            $csv->linefeed = "\n";

            $out = $csv->output("search.csv", $data[0], $data[1], ';');
        }
        else {
            $excel = Excel::create();
            $sheet = $excel->sheet();
            
            $sheet->writeRow($data[1]);
            $sheet->writeArrayTo('A2', $data[0]);

            Yii::$app->response->clearOutputBuffers();
            $excel->download("search.xlsx");
        }

        exit();
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
