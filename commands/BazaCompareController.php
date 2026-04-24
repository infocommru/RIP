<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Book;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;
use app\models\Record;
use Yii;

class BazaCompareController extends Controller {

    public function getCemeteryId($id) {
        if ($id <= 32)
            return $id;

        if (($id >= 40) && ($id <= 52))
            return $id;

        $translate = [
            33 => 34,
            34 => 35,
            35 => 36,
            36 => 37,
            37 => 38,
            38 => 39,
            39 => 33,
        ];

        if (!isset($translate[$id]))
            return false;

        return $translate[$id];
    }

    private function cmp_record12($record1, $record2) {
        $fields_cmp = [
            'numReg', 'numLiteral', 'age', 'death_date', 'rip_date', 'docnum',
            'fio', 'zags', 'area_num', 'row_num', 'rip_num'
        ];

        foreach ($fields_cmp as $f) {
            if ($record1[$f] != $record2[$f]) {
                return false;
            }
        }

        return true;
    }

    public function actionIndex($cemetery_id = 0) {
        $baza_ripdump = "rip_dump";

        $cemeteries = Cemetery::find()->orderBy('id')->all();
        if ($cemetery_id)
            $cemeteries = Cemetery::find()->andWhere(['id' => $cemetery_id])->all();

        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;
            $c_id2 = $this->getCemeteryId($c_id);

            if (!$c_id2) {
                echo $cemetery->name . " not found";
                exit;
            }



            $books = Book::find()
                    ->andWhere(['cemetery_id' => $cemetery->id])
                    ->all();

            foreach ($books as $book) {
                if ($book->part_id) {
                    $part = Part::find()->andWhere(['id' => $book->part_id])->one();
                    if (!$part)
                        continue;
                    if ($part->status_result != 1)
                        continue;
                }

                $svazka = $book->svazka;
                $number = $book->number;
                $bName = $book->name;

                $sql = <<<"HERE"
            select * from $baza_ripdump.book 
                where 
            cemetery_id = $c_id2 
            and svazka = '$svazka' 
            and number = '$number'            
            and name = '$bName'
HERE;

                $book_c2 = Yii::$app->db->createCommand($sql)
                        ->queryAll();

                if (sizeof($book_c2) < 1) {
                    $book->dbg = 1;
                    $book->save();
                    echo $book->name . "\r\n";
                    continue;
                    //exit;
                }

                if (sizeof($book_c2) > 1) {
                    print_r($book_c2);
                    //continue;
                    //continue;
                    //exit;
                }

                $book_c2 = $book_c2[0];

                $records = \app\models\Record::find()
                        ->andWhere(['book_id' => $book->id])
                        ->andWhere(['deleted' => 0])
                        ->andWhere('dubl < 1')
                        ->orderBy('id')
                        ->asArray()
                        ->all();

                $sql = <<<"HERE"
            select * from $baza_ripdump.record 
                where 
            book_id = $book_c2[id] 
            and deleted = 0 
            and dubl < 1            
            order by id
HERE;

                $records_c2 = Yii::$app->db->createCommand($sql)
                        ->queryAll();

                $lastPage = 'asdasd';
                $lastPagePunkt = 1;
                foreach ($records as $record) {
                    $found = false;
                    foreach ($records_c2 as $record_c2) {
                        if ($this->cmp_record12($record, $record_c2)) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $record = Record::find()->andWhere(['id' => $record['id']])->one();
                        $record->bad_flag = 100;
                        $record->save();
                    }
                }
            }
        }
    }
}
