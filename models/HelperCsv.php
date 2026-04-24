<?php

namespace app\models;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class HelperCsv {

    public static function processBookCsv($bookId, $filepath, $bad_flag = 0) {
        $riper_exists = true;

        $csv_data = file_get_contents($filepath);
        if (!substr_count($csv_data, ",RIPER,")) {
            $riper_exists = false;
        }
        /*
          $index_riper = [
          7=>7,
          8=>8,
          9=>9,
          10=>10,
          11=>11,
          15=>15,
          16=>16,
          17=>17,
          ];

          if(!$riper_exists){
          $index_riper = [
          7=>8,
          8=>9,
          9=>10,
          10=>11,
          11=>10,
          15=>14,
          16=>15,
          17=>16,
          ];
          }
         */
        $csv = new \ParseCsv\Csv();
        //$csv->offset = 1;
        $csv->delimiter = ",";
        $csv->parseFile($filepath);

        $counter = 0;

        $statInfo = [
            'year1' => null,
            'year2' => null,
            'records' => 0
        ];

        $filenames = [];
        
        foreach ($csv->data as $row) {
            $query_last = \app\models\Record::find()->andWhere(['book_id' => $bookId]);
            $record = new \app\models\Record();
            $record->book_id = $bookId;
            $cnt = 0;
            $valid = true;

            $nReg = false;

            foreach ($row as $k => $v) {
                switch ($cnt) {
                    case 0:
                        $vv = (intval($v)) . "";
                        $query_last->andWhere(['book_id' => $record->book_id]);
                        if ($v == $vv) {
                            $v = intval($v);
                            if ($v > 1000000)
                                $v = 0;
                            $record->numReg = $v;
                            $nReg = $v;
                            $query_last->andWhere(['numReg' => $v]);
                        } else {
                            $record->numLiteral = $v;
                            $nReg = $v;

                            if ($v)
                                $query_last->andWhere(['numLiteral' => $v]);
                        }

                        if (!$v) {
                            $counter++;
                        }

                        if ($v === "NumReg") {
                            $valid = false;
                        }

                        //if ($query_last->one()) {
                        //    if ($v)
                        //        $valid = false;
                        //}

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
                        if (preg_match('#(\d\d\d\d)#', $v, $m)) {
                            $ddate = intval($m[1]);
                            if (($ddate < 2030) && ($ddate > 1700)) {
                                if (!$statInfo['year1'])
                                    $statInfo['year1'] = $ddate;

                                if (!$statInfo['year2'])
                                    $statInfo['year2'] = $ddate;

                                if ($statInfo['year1'] > $ddate)
                                    $statInfo['year1'] = $ddate;
                                if ($statInfo['year2'] < $ddate)
                                    $statInfo['year2'] = $ddate;
                            }
                        }
                        break;
                    case 5:
                        $record->docnum = $v;
                        break;
                    case 6:
                        $record->zags = $v;
                        if (!$riper_exists) {
                            $cnt++;
                        }
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

                        if (!isset($filenames[$v]))
                            $filenames[$v] = [];

                        if (!in_array($nReg, $filenames[$v]))
                            $filenames[$v][] = $nReg;

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

            $one_last_all = $query_last->all();
            $attrb = ['id', 'numLiteral', 'user_id', 'updated_at', 'vopros', 'is_unknown', 'dubl', 'gos', 'bad_flag', 'deleted'];
            
            foreach ($one_last_all as $one_last) {
                //if (($one_last->fio == $record->fio) || (!$record->fio)) {
                if (($one_last->getAttributes(null, $attrb) == $record->getAttributes(null, $attrb)) || !$record->fio){
                	if (($one_last->numLiteral === $record->numLiteral)){
                		$valid = false;
                	}
                	else if (($one_last->numLiteral === '') && ($record->numLiteral === null)){
                		$valid = false;
                	}
                    
                    //print_r($one_last);//exit;
                //else if (($one_last->numLiteral !== null) && ($record->numLiteral === null)) { $valid = false; }
                } else {
                    //print_r($one_last);//print_r($record);
                    //exit;
                }
            }

            if (($record->numReg === null) && (!$record->numLiteral)){
                $valid = false;
            }

            if (!$record->fio) {
                $valid = false;
            }

            //$f_notsave = '/var/www/html/temp/not_save.txt';

            if ($valid) {
                //$record->bad_flag = 2; //$bad_flag;
                if (!$record->save()) {
                    //print_r($record);
                }
            }
        }

        $statInfo['records'] = \app\models\Record::find()->andWhere(['book_id' => $bookId])->count();

        $rCount = [];
        foreach ($filenames as $fName => $list) {
            $cnt = sizeof($list);
            if (!isset($rCount[$cnt]))
                $rCount[$cnt] = 0;

            $rCount[$cnt]++;
        }

        $max_key = 0;
        $max_key_val = 0;
        //print_r($filenames);
        //print_r($rCount );
        foreach ($rCount as $cnt => $val) {
            if ($val > $max_key_val) {
                $max_key = $cnt;
                $max_key_val = $val;
            }
        }

        $statInfo['per_page'] = $max_key;
        //print_r($statInfo);exit;
        return $statInfo;
    }
}
