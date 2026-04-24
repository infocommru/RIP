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
