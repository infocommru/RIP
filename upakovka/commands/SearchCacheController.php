<?php

namespace app\commands;

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

    public function actionIndex($cemetery_id = 0) {
        /*
          $books = Book::find()->all();
          foreach($books as $book){
          HelperLevoshkin::setBookPart($book);
          }

          HelperLevoshkin::setPartRecords();
          exit;
         */


        $zags_list = Helper::regions();
        $cemeteries = Cemetery::find()->orderBy('id')->all();
        if ($cemetery_id)
            $cemeteries = Cemetery::find()->andWhere(['id' => $cemetery_id])->all();

        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;

            $table_name = "__search_form_$c_id";

            $sql = <<<"HERE"
                    
     drop TABLE if exists `$table_name`;                  
HERE;

            $result = \Yii::$app->getDb()->createCommand($sql)->execute();

            $sql = <<<"HERE"
                    
     CREATE TABLE `$table_name` (
  `id` int(11) NOT NULL,
  `record_id` int(11) NOT NULL,
  `regnum` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fam` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nam` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ot` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `dead_year` int(11) DEFAULT NULL,
  `rip_year` int(11) DEFAULT NULL,
  `zags_num` int(11) NOT NULL DEFAULT -1,
  `rip_style` int(11) NOT NULL DEFAULT -1,
  `unknown` int(11) NOT NULL DEFAULT 0,
  `unknown_number` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `docnum` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `areanum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rownum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ripnum` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `relative` varchar(256) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `svazka_num` varchar(32) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
  `book_num` varchar(32) COLLATE utf8mb4_unicode_ci  DEFAULT NULL,
  `page_num` varchar(64)  COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_punkt` int(11) DEFAULT NULL


) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;               
                    
HERE;

            $result = \Yii::$app->getDb()->createCommand($sql)->execute();

            $sql = <<<"HERE"
                    
ALTER TABLE `$table_name`
  ADD PRIMARY KEY (`id`),
  ADD KEY `record_id` (`record_id`),
  ADD KEY `regnum` (`regnum`),
  ADD KEY `fam` (`fam`),
  ADD KEY `nam` (`nam`),
  ADD KEY `ot` (`ot`),
  ADD KEY `age` (`age`),
  ADD KEY `dead_year` (`dead_year`),
  ADD KEY `rip_year` (`rip_year`),
  ADD KEY `zags_num` (`zags_num`),
  ADD KEY `unknown_number` (`unknown_number`),
  ADD KEY `rip_style` (`rip_style`);
             
HERE;

            $result = \Yii::$app->getDb()->createCommand($sql)->execute();

            $sql = <<<"HERE"
                    
ALTER TABLE `$table_name`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
HERE;

            $result = \Yii::$app->getDb()->createCommand($sql)->execute();

            $books = Book::find()
                    ->andWhere(['cemetery_id' => $cemetery->id])
                    ->all();

            $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

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
                        ->orderBy('id')
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
                        "rip_year" => null,
                    ];
                    $fio = $record->fio;
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


                    $r_new['age'] = intval($record->age);
                    if ($r_new['age'] > 200)
                        $r_new['age'] = null;

                    if (preg_match("#(\d\d\d\d)#", $record->death_date, $m)) {
                        $r_new['dead_year'] = $m[1];
                    }

                    if (preg_match("#(\d\d\d\d)#", $record->rip_date, $m)) {
                        $r_new['rip_year'] = $m[1];
                    }

                    $r_new['docnum'] = $record->docnum;
                    $r_new['areanum'] = $record->area_num;
                    $r_new['rownum'] = $record->row_num;
                    $r_new['ripnum'] = $record->rip_num;
                    $r_new['relative'] = $record->relative_fio;
                    $r_new['rip_style'] = $record->rip_style;

                    $zIndex = array_search($record->zags, $zags_list);
                    if ((!$zIndex) && ($zags_list[0] != $record->zags)) {
                        $r_new['zags'] = -1;
                    } else {
                        $r_new['zags'] = $zIndex;
                    }

                    $sfb = new \app\models\SearchFormBasic();
                    $sfb->record_id = $record->id;
                    $sfb->fam = $r_new['fam'];
                    $sfb->nam = $r_new['nam'];
                    $sfb->ot = $r_new['ot'];
                    $sfb->age = intval($r_new['age']);
                    $sfb->dead_year = $r_new['dead_year'];
                    $sfb->rip_year = $r_new['rip_year'];
                    $sfb->docnum = $r_new['docnum'];
                    $sfb->areanum = $r_new['areanum'];
                    $sfb->rownum = $r_new['rownum'];
                    $sfb->ripnum = $r_new['ripnum'];
                    $sfb->relative = $r_new['relative'];
                    $sfb->zags_num = $r_new['zags'];
                    $sfb->unknown = $record->is_unknown;
                    $sfb->rip_style = $r_new['rip_style'];
                    //////////////////////////////
                    $sfb->svazka_num = $book->svazka;
                    $sfb->book_num = $book->number;

                    if ($record->numReg) {
                        $sfb->regnum = $record->numReg;
                    } else {
                        $sfb->regnum = $record->numLiteral;
                    }

                    if (preg_match("#№\s+([\d\\/]+)#", $record->fio, $m)) {
                        //echo $record->fio;
                        //print_r($m);
                        $sfb->unknown_number = $m[1];
                        //exit;
                    } else {
                        if ($record->is_unknown) {
                            if (preg_match("#.*?(\d[\d\\/]+).*?#", $record->fio, $m)) {
                                $sfb->unknown_number = $m[1];
                                //echo $record->fio . "\n";
                                //print_r($m);
                                //exit;
                            } else {
                                if (preg_match("#.*?(\d+).*?#", $record->fio, $m)) {
                                    $sfb->unknown_number = $m[1];
                                    //echo $record->fio . "\n";
                                    //print_r($m);
                                    //exit;
                                }
                            }
                        }
                    }

                    if ((($record->is_unknown) && (!$sfb->unknown_number))) {
                        echo $record->fio . "\n";
                        //exit;
                    }

                    //echo $record->filename;
                    $fname = strtr($record->filename, ["\\" => '/']);
                    if (preg_match("#.*?/([^/]*?)\.jp.*?$#", $fname, $pmatch)) {
                        //echo $record->filename;
                        //print_r($pmatch);
                        $sfb->page_num = $pmatch[1];
                        //exit;
                    } else {
                        //echo 11;
                        //exit;
                    }

                    if ($sfb->page_num != $lastPage) {
                        $lastPage = $sfb->page_num;
                        $lastPagePunkt = 1;
                    }

                    $sfb->page_punkt = $lastPagePunkt++;

                    //echo $sfb->age . ',';
                    if (!$sfb->save()) {
                        //print_r($sfb);
                        //exit;
                    }
                }
            }
        }

        //$this->debug_log();
        exit;
    }

    private function debug_log() {
        $record_ids = [];
        $cemeteries = Cemetery::find()->all();
        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;
            $table_name = "__search_form_$c_id";
            $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

            $rSearch = \app\models\SearchFormBasic::find()->all();
            foreach ($rSearch as $search) {
                $record_ids[] = $search->record_id;
            }
        }

        //print_r( $record_ids);

        $result = \Yii::$app->getDb()->createCommand("delete from record2 where 1")->execute();

        $records = \app\models\Record::find()
                ->andWhere(['not in', 'id', $record_ids])
                ->all();

        foreach ($records as $record) {
            $r = new \app\models\Record2();
            $r->attributes = $record->attributes;
            $r->save();
        }

        //echo sizeof($records);exit;
    }
}
