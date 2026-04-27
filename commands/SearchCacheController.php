<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Book;
use app\models\Part;
use app\models\Helper;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;

class SearchCacheController extends Controller {

    private function getDate($sDate) {
        $result = [
            'day' => null,
            'month' => null,
            'year' => null,
            'date' => null,
        ];

        if (preg_match("#(\d\d\d\d)#", $sDate, $m)) {
            $result['year'] = $m[1];
        }

        if (!$result['year'])
            return false;

        if (preg_match("#(\d\d?)\D(\d\d?)\D(\d\d\d\d)#", $sDate, $m)) {
            $result['day'] = intval(ltrim($m[1], '0'));
            $result['month'] = intval(ltrim($m[2], '0'));
        }

        if (preg_match("#(\d\d\d\d)\D(\d\d?)\D(\d\d?)#", $sDate, $m)) {
            $result['day'] = intval(ltrim($m[3], '0'));
            $result['month'] = intval(ltrim($m[2], '0'));
        }

        if (($result['year']) && (strlen($result['year'] . '') == 4)) {
            $result['date'] = $result['year'] . '';
        } else {
            $result['date'] = '1000';
        }

        if ($result['month']) {
            if ($result['month'] <= 9) {
                $result['date'] .= '0' . $result['month'];
            } else {
                $result['date'] .= $result['month'];
            }
        } else {
            $result['date'] .= '00';
        }

        if ($result['day']) {
            if ($result['day'] <= 9) {
                $result['date'] .= '0' . $result['day'];
            } else {
                $result['date'] .= $result['day'];
            }
        } else {
            $result['date'] .= '00';
        }

        $result['date'] = intval($result['date']);

        return $result;
    }
    
    public function actionIndex($cemetery_id = 0) {
        //$zags_list = Helper::regions();
        $cemeteries = Cemetery::find()->orderBy('id')->all();
        if ($cemetery_id)
            $cemeteries = Cemetery::find()->andWhere(['id' => $cemetery_id])->all();
           
        foreach ($cemeteries as $cemetery) { 
		$c_id = $cemetery->id;
		$table_name = "__search_form_$c_id";
		
		Yii::$app->db->createCommand("DROP TABLE IF EXISTS `$table_name`;")->execute();
		Yii::$app->db->createCommand("
        	CREATE TABLE `$table_name` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`record_id` int(11) NOT NULL,
			`regnum` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`fam` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`nam` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`ot` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`age` int(11) DEFAULT NULL,
			`dead_year` int(11) DEFAULT NULL,
			`dead_month` int(11) DEFAULT NULL,
			`dead_day` int(11) DEFAULT NULL,
			`dead_date` int(11) DEFAULT NULL,
			`rip_year` int(11) DEFAULT NULL,
			`rip_month` int(11) DEFAULT NULL,
			`rip_day` int(11) DEFAULT NULL,
			`rip_date` int(11) DEFAULT NULL,
			`zags` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`zags_id` int(11) DEFAULT NULL,
			`rip_style` int(11) NOT NULL DEFAULT -1,
			`unknown` int(11) NOT NULL DEFAULT 0,
			`unknown_number` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`docnum` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`areanum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`rownum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`ripnum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`relative` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`svazka_num` varchar(32) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
			`book_num` varchar(32) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
			`page_num` varchar(64)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`page_punkt` int(11) DEFAULT NULL,
			`comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`comment_book` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
			`book_id` int(11) NOT NULL DEFAULT 0,
			`book_rip_style` int(11) NOT NULL DEFAULT 0,
		        PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;")->execute();
	
		$books = Book::find()
                   ->andWhere(['cemetery_id' => $cemetery->id])
                   ->all();

            $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;
            $batchRows = [];

            foreach ($books as $book) {
                if ($book->part_id) {
                    $part = Part::find()->andWhere(['id' => $book->part_id])->one();
                    if (!$part)
                        continue;
                    if ($part->status_result != 1)
                        continue;
                }
                $records = \app\models\Record::find()
                        ->andWhere(['book_id' => $book->id])
                        ->andWhere(['deleted' => 0])
                        #->andWhere('dubl < 1')
                        ->orderBy('id')
                        ->asArray()
                        ->all();

                $lastPage = 'asdasd';
                $lastPagePunkt = 1;
                foreach ($records as $record) {
                    $r_new = [
                        "fam" => null,
                        "nam" => null,
                        "ot" => null,
                        "age" => null,
                        "dead_year" => null,
                        "dead_month" => null,
                        "dead_day" => null,
                        "rip_year" => null,
                        "rip_month" => null,
                        "rip_day" => null,
                    ];
                    $fio = $record['fio'];
                    for ($j = 0; $j < 5; $j++) {
                        $fio = strtr(trim($fio), ["  " => " ", "\t" => ' ']);
                    }
                    if ($fio) {
                        $ff = explode(" ", $fio);
                        $r_new['fam'] = $ff[0];
                        if (sizeof($ff) > 1)
                            $r_new['nam'] = $ff[1];
                        if (sizeof($ff) > 2)
                            $r_new['ot'] = $ff[2];
                    }


                    $r_new['age'] = intval($record['age']);
                    if ($r_new['age'] > 200)
                        $r_new['age'] = null;

                    $r_new['docnum'] = $record['docnum'];
                    $r_new['areanum'] = $record['area_num'];
                    $r_new['rownum'] = $record['row_num'];
                    $r_new['ripnum'] = $record['rip_num'];
                    $r_new['relative'] = $record['relative_fio'];
                    $r_new['rip_style'] = $record['rip_style'];

                    //$zIndex = array_search($record['zags'], $zags_list);
                    //if ((!$zIndex) && ($zags_list[0] != $record['zags'])) {
                    //    $r_new['zags'] = -1;
                    //} else {
                    //    $r_new['zags'] = $zIndex;
                    //}
                    $r_new['zags'] = $record['zags'];

                    $deadYearInf = $this->getDate($record['death_date']);
                    $ripYearInf = $this->getDate($record['rip_date']);

                    if ($deadYearInf) {
                        $r_new['dead_year'] = $deadYearInf['year'];
                        $sfb_dead_month = $deadYearInf['month'];
                        $sfb_dead_day = $deadYearInf['day'];
                        $sfb_dead_date = $deadYearInf['date'];
                    }

                    if ($ripYearInf) {
                        $r_new['rip_year'] = $ripYearInf['year'];
                        $sfb_rip_month = $ripYearInf['month'];
                        $sfb_rip_day = $ripYearInf['day'];
                        $sfb_rip_date = $ripYearInf['date'];
                    }

                    $sfb_record_id = $record['id'];
                    $sfb_fam = $r_new['fam'];
                    $sfb_nam = $r_new['nam'];
                    $sfb_ot = $r_new['ot'];
                    $sfb_age = intval($r_new['age']);
                    $sfb_dead_year = $r_new['dead_year'];
                    $sfb_rip_year = $r_new['rip_year'];
                    $sfb_docnum = $r_new['docnum'];
                    $sfb_areanum = $r_new['areanum'];
                    $sfb_rownum = $r_new['rownum'];
                    $sfb_ripnum = $r_new['ripnum'];
                    $sfb_relative = $r_new['relative'];
                    $sfb_zags = $r_new['zags'];
                    $sfb_unknown = $record['is_unknown'];
                    $sfb_rip_style = $r_new['rip_style'];
                    //////////////////////////////
                    $sfb_svazka_num = $book->svazka;
                    $sfb_book_num = $book->number;
                    $sfb_comment_book = $book->comment;
                    $sfb_book_rip_style = $book->rip_style;
                    $sfb_book_id = $book->id;

                    if ($record['numReg']) {
                        $sfb_regnum = $record['numReg'];
                    } else {
                        $sfb_regnum = $record['numLiteral'];
                    }

                    $sfb_comment = $record['comment'];
                    $sfb_unknown_number = null;

                    if (preg_match("#№\s+([\d\\/]+)#", $record['fio'], $m)) {
                        $sfb_unknown_number = $m[1];
                    } else {
                        if ($record['is_unknown']) {
                            if (preg_match("#.*?(\d[\d\\/]+).*?#", $record['fio'], $m)) {
                                $sfb_unknown_number = $m[1];
                            } else {
                                if (preg_match("#.*?(\d+).*?#", $record['fio'], $m)) {
                                    $sfb_unknown_number = $m[1];
                                }
                            }
                        }
                    }

                    if ((($record['is_unknown']) && (!$sfb_unknown_number))) {
                        echo $record['fio'] . "\n";
                    }

                    $fname = strtr($record['filename'], ["\\" => '/']);
                    if (preg_match("#.*?/([^/]*?)\.jp.*?$#", $fname, $pmatch)) {
                        $sfb_page_num = ltrim($pmatch[1], "0");
                    }

                    if ($sfb_page_num != $lastPage) {
                        $lastPage = $sfb_page_num;
                        $lastPagePunkt = 1;
                    }

                    $sfb_page_punkt = $lastPagePunkt++;
                    
                    $batchRows[] = [
                    	$sfb_record_id,
                    	$sfb_regnum,
                    	$sfb_fam,
                    	$sfb_nam,
                    	$sfb_ot,
                    	$sfb_age,
                    	$sfb_dead_year,
                    	$sfb_dead_month,
                    	$sfb_dead_day,
                    	$sfb_dead_date,
                    	$sfb_rip_year,
                    	$sfb_rip_month,
                    	$sfb_rip_day,
                    	$sfb_rip_date,
                    	$sfb_zags,
                    	$sfb_rip_style,
                    	$sfb_unknown,
                    	$sfb_unknown_number,
                    	$sfb_docnum,
                    	$sfb_areanum,
                    	$sfb_rownum,
                    	$sfb_ripnum,
                    	$sfb_relative,
                    	$sfb_svazka_num,
                    	$sfb_book_num,
                    	$sfb_page_num,
                    	$sfb_page_punkt,
                    	$sfb_comment,
                    	$sfb_comment_book,
                    	$sfb_book_id,
                    	$sfb_book_rip_style
                    ];
                    
                    if (count($batchRows) >= 1000) {
                    Yii::$app->db->createCommand()->batchInsert(
                        $table_name,
		                [
		                    "record_id", "regnum", "fam", "nam", "ot", "age", "dead_year", "dead_month", 
		                    "dead_day", "dead_date", "rip_year", "rip_month", "rip_day", "rip_date", "zags",
		                    "rip_style", "unknown", "unknown_number", "docnum", "areanum", "rownum", "ripnum",
		                    "relative", "svazka_num", "book_num", "page_num", "page_punkt", "comment", "comment_book",
		                    "book_id", "book_rip_style"
		                ],
		                $batchRows
                    	)->execute();
                    	$batchRows = [];
                    }
                }
            }
            
            if ($batchRows) {
                Yii::$app->db->createCommand()->batchInsert(
                    $table_name,
                    [
                        "record_id", "regnum", "fam", "nam", "ot", "age", "dead_year", "dead_month", 
                        "dead_day", "dead_date", "rip_year", "rip_month", "rip_day", "rip_date", "zags",
                        "rip_style", "unknown", "unknown_number", "docnum", "areanum", "rownum", "ripnum",
                        "relative", "svazka_num", "book_num", "page_num", "page_punkt", "comment", "comment_book",
                        "book_id", "book_rip_style"
                    ],
                        $batchRows
                    )->execute();
                $batchRows = [];
            }    
            
            Yii::$app->db->createCommand("
           		ALTER TABLE `$table_name`
		  		ADD KEY `record_id` (`record_id`),
		  		ADD KEY `regnum` (`regnum`),
				ADD KEY `fam` (`fam`),
				ADD KEY `nam` (`nam`),
				ADD KEY `ot` (`ot`),
				ADD KEY `age` (`age`),
				ADD KEY `dead_year` (`dead_year`),
				ADD KEY `dead_month` (`dead_month`),
				ADD KEY `dead_day` (`dead_day`),
				ADD KEY `dead_date` (`dead_date`),
				ADD KEY `rip_year` (`rip_year`),
				ADD KEY `rip_month` (`rip_month`),
				ADD KEY `rip_day` (`rip_day`),
				ADD KEY `rip_date` (`rip_date`),
				ADD KEY `zags` (`zags`),
				ADD KEY `zags_id` (`zags_id`),
				ADD KEY `unknown_number` (`unknown_number`),
				ADD KEY `rip_style` (`rip_style`),
				ADD KEY `book_rip_style` (`book_rip_style`),
				ADD KEY `book_id` (`book_id`),
				ADD KEY `areanum` (`areanum`),
				ADD KEY `rownum` (`rownum`),
				ADD KEY `ripnum` (`ripnum`);
        	")->execute();
       	}
        exit;
    }
}
