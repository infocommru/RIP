<?php

namespace app\models;

class HelperCache {
    public static function deleteCemetery($cemetery) {
        $response = \Yii::$app->elasticsearch->post(
            [ \app\models\CacheRecords::index(), '_delete_by_query' ],
            ['wait_for_completion' => 'false',],
            json_encode([
                'query' => [
                    'term' => ['cemetery_id' => $cemetery]
                ]
            ])
        );
    }

    public static function deleteBook($book) {
        $response = \Yii::$app->elasticsearch->post(
            [ \app\models\CacheRecords::index(), '_delete_by_query' ],
            ['wait_for_completion' => 'false',],
            json_encode([
                'query' => [
                    'term' => ['book_id' => $book]
                ]
            ])
        );
    }

	public static function updateCache($books){
            $batchRows = CacheRecords::getDb()->createBulkCommand();
            $counter = 0;

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
                        "age_int" => null,
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


                    $r_new['age_int'] = intval($record['age']);
                    if ($r_new['age_int'] > 200)
                        $r_new['age_int'] = null;

                    $r_new['docnum'] = $record['docnum'];
                    $r_new['areanum'] = $record['area_num'];
                    $r_new['rownum'] = $record['row_num'];
                    $r_new['ripnum'] = $record['rip_num'];
                    $r_new['relative'] = $record['relative_fio'];
                    $r_new['rip_style'] = $record['rip_style'];
                    $r_new['zags'] = $record['zags'];

                    $deadYearInf = \app\models\HelperLevoshkin::getDate($record['death_date']);
                    $ripYearInf = \app\models\HelperLevoshkin::getDate($record['rip_date']);

                    $r_new['dead_year'] = $deadYearInf['year'];
                    $sfb_dead_month = $deadYearInf['month'];
                    $sfb_dead_day = $deadYearInf['day'];
                    $sfb_dead_date = $deadYearInf['date'];

                    $r_new['rip_year'] = $ripYearInf['year'];
                    $sfb_rip_month = $ripYearInf['month'];
                    $sfb_rip_day = $ripYearInf['day'];
                    $sfb_rip_date = $ripYearInf['date'];

                    $sfb_record_id = $record['id'];
                    $sfb_fam = $r_new['fam'];
                    $sfb_nam = $r_new['nam'];
                    $sfb_ot = $r_new['ot'];
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

                    /*if ((($record['is_unknown']) && (!$sfb_unknown_number))) {
                        echo $record['fio'] . "\n";
                    }*/

                    $sfb_page_num = '';

                    $fname = strtr($record['filename'], ["\\" => '/']);
                    if (preg_match("#.*?/([^/]*?)\.jp.*?$#", $fname, $pmatch)) {
                        $sfb_page_num = ltrim($pmatch[1], "0");
                    }

                    if ($sfb_page_num != $lastPage) {
                        $lastPage = $sfb_page_num;
                        $lastPagePunkt = 1;
                    }

                    $sfb_page_punkt = $lastPagePunkt++;
                                     
                    $batchRows->addAction(
                    	[
		                	'index' => [
						        '_index' => \app\models\CacheRecords::index(),
						        '_id'    => $sfb_record_id,
				        	]
		            	],
                    	[
		                	'record_id' => $sfb_record_id,
                            'cemetery_id' => $book->cemetery_id,
		                	'regnum' => $sfb_regnum,
		                	'fam' => $sfb_fam,
		                	'nam' => $sfb_nam,
		                	'fio_display' => $record['fio'],
		                	'ot' => $sfb_ot,
		                	'age' => $record['age'],
		                	'age_int' => $r_new['age_int'],
		                	'dead_year' => $sfb_dead_year,
		                	'dead_month' => $sfb_dead_month,
		                	'dead_day' => $sfb_dead_day,
		                	'dead_date' => $sfb_dead_date,
		                	'rip_year' => $sfb_rip_year,
		                	'rip_month' => $sfb_rip_month,
		                	'rip_day' => $sfb_rip_day,
		                	'rip_date' => $sfb_rip_date,
		                	'zags' => $sfb_zags,
		                	'rip_style' => $sfb_rip_style,
		                	'unknown' => $sfb_unknown,
		                	'unknown_number' => $sfb_unknown_number,
		                	'docnum' => $sfb_docnum,
		                	'areanum' => $sfb_areanum,
		                	'rownum' => $sfb_rownum,
		                	'ripnum' => $sfb_ripnum,
		                	'relative' => $sfb_relative,
		                	'svazka_num' => $sfb_svazka_num,
		                	'book_num' => $sfb_book_num,
		                	'page_num' => $sfb_page_num,
		                	'page_punkt' => $sfb_page_punkt,
		                	'comment' => $sfb_comment,
		                	'comment_book' => $sfb_comment_book,
		                	'book_id' => $sfb_book_id,
		                	'book_rip_style' => $sfb_book_rip_style,
		                	'filename' => $record['filename'],
		                	'vopros' => $record['vopros'],
		                	'updated_at' => $record['updated_at'],
		                ]);
		                
		                $counter++;

                    if ($counter >= 5000) {
                    	$response = $batchRows->execute();
                    	$batchRows = CacheRecords::getDb()->createBulkCommand();
                    	$counter = 0;
                    }
                }
            }
            
            if ($counter > 0) {
            	$response = $batchRows->execute();
                $batchRows = CacheRecords::getDb()->createBulkCommand();
                $counter = 0;
            }
	}
}
