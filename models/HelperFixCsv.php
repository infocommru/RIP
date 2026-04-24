<?php

namespace app\models;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

//id last 2625066    2625145
// 1419761
// SELECT `numReg`,count(*) c FROM `record` WHERE book_id=1889 group by `numReg` order by -c; 
class HelperFixCsv {

    public static function processBookCsv($bookId, $filepath, $bookName) {
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

                        if ($v == "NumReg") {
                            $valid = false;
                        }

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

            $notFound = true;
            $invalid = '';

            $one_last_all = $query_last->all();
            foreach ($one_last_all as $one_last) {
                if (($one_last->fio == $record->fio) || (!$record->fio)) {
                    $invalid = 'fio';
                    $valid = false;

                    //print_r($one_last);exit;
                } else {
                    //print_r($one_last);print_r($record);
                    //exit;
                }
            }

            //if ($one_last = $query_last->one()) {
            //$notFound = false;
            //}

            $query_test = \app\models\Record::find()
                    ->andWhere(['book_id' => $bookId]);

            if ($record->numReg) {
                $query_test->andWhere(['numReg' => $record->numReg]);
            }

            if ($record->numLiteral) {
                $query_test->andWhere(['numLiteral' => $record->numLiteral]);
            }

            if ((!$record->numReg ) && (!$record->numLiteral )) {
                $invalid .= ',nreg';
                $valid = false;
            }

            if (!$record->fio) {
                //$notFound = true;
                $invalid .= ',fio2';
                $valid = false;
            }

            $query_test->andWhere(['fio' => $record->fio]);

            if ($query_test->one()) {
                $notFound = false;
                $invalid .= ',fio3';
                $valid = false;
            }

            $ddata = [
                $nReg,
                $record->fio,
                $record->age,
                $record->death_date,
                $record->docnum,
                $record->zags,
                $record->riper,
                $record->area_num,
                $record->row_num,
                $record->rip_num,
                $record->relative_fio,
            ];

            $d = implode('|', $ddata);

            $f_notfound = '/var/www/html/temp/not_found.txt';
            $f_notsave = '/var/www/html/temp/not_save.txt';

            $f_book = '/var/www/html/temp/lostbook/' . $bookName . '.txt';

            if ($notFound) {
                //$d = $filepath.';'.$d;
                file_put_contents($f_notfound, $filepath . ';' . $d . "\r\n", FILE_APPEND);
            }

            if ($valid) {
                $record->bad_flag = 3;
                if (!$record->save()) {
                    file_put_contents($f_notsave, $filepath . ';' . $d . "\r\n", FILE_APPEND);
                    //print_r($record);
                } else {
                    //print_r($record);exit;
                }
            }

            if ((!$valid) || ($notFound)) {
                if (!substr_count($invalid, 'fio3')) {
                    $t = "valid=$valid,nf=$notFound";
                    file_put_contents($f_book, $t . ';' . $invalid . ';' . $d . "\r\n", FILE_APPEND);
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
