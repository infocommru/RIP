<?php

namespace app\models;

class HelperLevoshkin {

    public static function region($txt) {
        $txt_lower = mb_strtolower($txt, 'utf-8');
        $regions = Helper::readFileToList("../data/spb_region.txt");
        $max = false;
        foreach ($regions as $reg) {
            $reg_lower = mb_strtolower($reg, 'utf-8');
            similar_text($txt_lower, $reg_lower, $percent);

            if ((!$max) || ($max[1] < $percent)) {
                $max = [$reg, $percent];
            }
        }

        return $max;
    }

    public static function partMaxRecords() {
        return 5000;
    }

    public static function partPercent() {
        return 5;
    }

    public static function setBookPart($book) {
        if ($book->part_id)
            return;

        $part = Part::find()
                ->andWhere(['cemetery_id' => $book->cemetery_id])
                ->andWhere(['<', 'records', self::partMaxRecords()])
                ->one();

        if ($part) {
            $book->part_id = $part->id;
            $book->save();
            $part->records += intval($book->records);
            $part->update_flag = 1;
            $part->save();
            //echo "records:" . $part->records . "\n";
            return true;
        } else {
            $cnt = Part::find()
                    ->andWhere(['cemetery_id' => $book->cemetery_id])
                    ->count();

            $part = new Part();
            $part->number = $cnt + 1;
            $part->cemetery_id = $book->cemetery_id;
            $part->add_at = time();
            $part->records = 0;
            $part->records += intval($book->records);
            $part->update_flag = 1;
            $part->save();
            $book->part_id = $part->id;
            $book->save();
        }
    }

    public static function setPartRecords() {
        $uParts = Part::find()
                ->andWhere('update_flag > 0')
                ->all();

        foreach ($uParts as $part) {
            $pId = $part->id;
            $books = Book::find()
                    ->andWhere(['part_id' => $part->id])
                    ->all();

            $bookIds = [];
            foreach ($books as $book) {
                $bookIds[] = $book->id;
            }

            $records = Record::find()
                    ->andWhere(['in', 'book_id', $bookIds])
                    ->all();

            $pSize = ceil(sizeof($records) * self::partPercent() * 1.0 / 100);

            shuffle($records);

            $sql = <<<"HERE"
           delete from part_record  where part_id = $pId;            
HERE;

            $result = \Yii::$app->getDb()->createCommand($sql)->execute();

            for ($i = 0; $i < $pSize; $i++) {
                $pr = new PartRecord();
                $pr->part_id = $pId;
                $pr->record_id = $records[$i]->id;
                $pr->save();
            }

            $part->update_flag = 0;
            $part->save();
        }
    }

    private static function getDate($sDate) {
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

    public static function updateSearchRecord($record) {
        $book = Book::find()
                ->andWhere(['id' => $record->book_id])
                ->one();

        $cemetery = Cemetery::find()
                ->andWhere(['id' => $book->cemetery_id])
                ->one();

        $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

        $zags_list = Helper::regions();

        $r_new = [
            "fam" => null,
            "nam" => null,
            "ot" => null,
            "age" => null,
            "dead_year" => null,
            "dead_month" => null,
            "dead_day" => null,
            "dead_date" => null,
            "rip_year" => null,
            "rip_month" => null,
            "rip_day" => null,
            "rip_date" => null,
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

        $r_new['dead_year'] = $record->death_date;
        $r_new['rip_year'] = $record->rip_date;

        $r_new['docnum'] = $record->docnum;
        $r_new['areanum'] = $record->area_num;
        $r_new['rownum'] = $record->row_num;
        $r_new['ripnum'] = $record->rip_num;
        $r_new['relative'] = $record->relative_fio;
        $r_new['rip_style'] = $record->rip_style;
        $r_new['zags'] = $record->zags;

        $sfb = \app\models\SearchFormBasic::find()
                ->andWhere(['record_id' => $record->id])
                ->one();

        if (!$sfb) {
            $sfb = new \app\models\SearchFormBasic();
        }

        $deadYearInf = self::getDate($r_new['dead_year']);
        $ripYearInf = self::getDate($r_new['rip_year']);

        $sfb->record_id = $record->id;
        $sfb->fam = $r_new['fam'];
        $sfb->nam = $r_new['nam'];
        $sfb->ot = $r_new['ot'];
        $sfb->age = intval($r_new['age']);

        $sfb->dead_year = $deadYearInf['year'];
        $sfb->dead_month = $deadYearInf['month'];
        $sfb->dead_day = $deadYearInf['day'];
        $sfb->dead_date = $deadYearInf['date'];

        $sfb->rip_year = $ripYearInf['year'];
        $sfb->rip_month = $ripYearInf['month'];
        $sfb->rip_day = $ripYearInf['day'];
        $sfb->rip_date = $ripYearInf['date'];

        $sfb->docnum = $r_new['docnum'];
        $sfb->areanum = $r_new['areanum'];
        $sfb->rownum = $r_new['rownum'];
        $sfb->ripnum = $r_new['ripnum'];

        $sfb->relative = $r_new['relative'];
        $sfb->zags = $r_new['zags'];
        $sfb->unknown = $record->is_unknown;
        if (!$sfb->unknown)
            $sfb->unknown = 0;
        $sfb->rip_style = $r_new['rip_style'];
        //////////////////////////////
        $sfb->svazka_num = $book->svazka;
        $sfb->book_num = $book->number;

        if ($record->numReg) {
            $sfb->regnum = $record->numReg;
        } else {
            $sfb->regnum = $record->numLiteral;
        }

        $sfb->comment = $record->comment;
        $sfb->comment_book = $book->comment;
        $sfb->book_id = $book->id;
        $sfb->book_rip_style = $book->rip_style;

        if (preg_match("#№\s+([\d\\/]+)#", $record->fio, $m)) {
            $sfb->unknown_number = $m[1];
        } else {
            if ($record->is_unknown) {
                if (preg_match("#.*?(\d[\d\\/]+).*?#", $record->fio, $m)) {
                    $sfb->unknown_number = $m[1];
                } else {
                    if (preg_match("#.*?(\d+).*?#", $record->fio, $m)) {
                        $sfb->unknown_number = $m[1];
                    }
                }
            }
        }

        if ((($record->is_unknown) && (!$sfb->unknown_number))) {
            
        }

        $fname = strtr($record->filename, ["\\" => '/']);
        if (preg_match("#.*?/([^/]*?)\.jp.*?$#", $fname, $pmatch)) {
            $sfb->page_num = ltrim($pmatch[1], "0");
        }

        $sfb->save();
    }

    public static function getUserPart() {
        $user = \Yii::$app->user->identity;

        $part = Part::find()
                ->andWhere(['user_id' => $user->id])
                ->andWhere(['status' => 1])
                ->one();

        return $part;
    }

    public static function getPartStatuses() {
        return [
            0 => 'не обработано',
            1 => 'в работе',
            2 => 'обработано',
        ];
    }

    public static function getPartRecordStatuses() {
        return [
            0 => 'не обработано',
            2 => 'плохая запись',
            3 => 'хорошая запись',
            4 => 'есть вопрос',
        ];
    }

    public static function getPartResultStatuses() {
        return [
            0 => 'не обработано',
            1 => 'хорошая партия',
            2 => 'плохая партия',
        ];
    }

    public static function getPartUploadStatuses() {
        return [
            0 => 'не обработано',
            1 => 'в обработке',
            2 => 'несоответствие книг',
            3 => 'обработано',
        ];
    }
}

