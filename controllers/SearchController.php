<?php

namespace app\controllers;

use app\models\Record;
use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \avadim\FastExcelWriter\Excel;
use app\models\HelperImg;
use Yii;

/**
 * RecordController implements the CRUD actions for Record model.
 */
class SearchController extends Controller {

    /**
     *
     * @var integer $searchLimit
     */
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

    /**
     *
     * @param string $q
     * @param string $variable
     * @return string
     */
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

    /**
     *
     * @param string $search_string
     * @param string $name_var
     * @return array<string, mixed>
     */
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

    /**
     *
     * @param string $search_string
     * @param string $name_var
     * @return array<string, mixed>
     */
    protected function searchStartFromConditions($search_string, $name_var) {
        $condition = [
            'prefix' => [
                $name_var . '.keyword' => $search_string
            ]
        ];

        return $condition;
    }

    /**
     *
     * @param string $search_string
     * @param string $name_var
     * @return array<string, mixed>
     */
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

    /**
     *
     * @param string $search_string
     * @param string $name_var
     * @return array<string, mixed>
     */
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

    /**
     *
     * @param int $switch
     * @param string $search_string
     * @param string $name_var
     * @return array<string, mixed>
     */
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
            default:
                $condition = [];
                break;
        }

        return $condition;
    }
    
    /**
     *
     * @param int $c_id
     * @return false|array{0: array<int, mixed>, 1: int}
     */
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

    	$query->query($elasticQuery);
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
			
        return [$result, $count];
    }

    /**
     *
     * @return string
     */
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
                    $search_data[] = [
                        'id' => $cemetery->id,
                        'name' => $cemetery->name,
                        'counter' => $counter,
                        'data' => $data
                    ];
                }
            }
        }

        return $this->render('index', [
        	'search_data' => $search_data
        ]);
    }

    /**
     *
     * @param int $c_id
     * @return array{0: array<int, array<int, mixed>>, 1: array<int, string>}
     */
    private function exportData(int $c_id): array {
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

    /**
     *
     * @param int $c_id
     * @return void
     */
    public function actionExport(int $c_id): void {
        $data = $this->exportData($c_id);

        $excel = Excel::create();
        $sheet = $excel->sheet();
        
        $sheet->writeRow($data[1]);
        $sheet->writeArrayTo('A2', $data[0]);

        Yii::$app->response->clearOutputBuffers();
        $excel->download("search.xlsx");

        exit();
    }

    /**
     *
     * @param int $record_id
     * @return void
     */
    public function actionVopros($record_id) {
        $record = Record::find()->andWhere(['id' => $record_id])->one();
        $record->vopros = 1;
        $record->save();
        echo $record->fio;
    }

    /**
     *
     * @param int $book_id
     * @return void
     */
    public function actionBookCover($book_id) {
        $book = Book::find()->andWhere(['id' => $book_id])->one();
        $titleBook = HelperImg::getTitleImage($book);
        
        if($titleBook){
            header("Location: $titleBook");
            exit;
        }
        else
            throw new NotFoundHttpException('Страница не найдена.');
    }
}