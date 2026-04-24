<?php

namespace app\models;

class Helper {

    public static function getCemeteryList() {
        $list = Cemetery::find()->all();
        $result = [];
        foreach ($list as $elem) {
            $result[$elem->id] = $elem->name;
        }

        return $result;
    }

    public static function getBookList($cemetery_id) {
        $list = Book::find()->andWhere(['cemetery_id' => $cemetery_id])->all();
        $result = [0 => "-"];
        foreach ($list as $elem) {
            $result[$elem->id] = $elem->name;
        }

        return $result;
    }

    public static function readFileToList($filename) {
        $result = [];
        $fp = fopen($filename, 'r');
        while (!feof($fp)) {
            $line = trim(fgets($fp));
            if ($line)
                $result[] = $line;
        }


        return $result;
    }

    public static function regions() {
        $filepath = "../data/spb_region.txt";
        if (!file_exists($filepath))
            $filepath = "./data/spb_region.txt";

        $lines = self::readFileToList($filepath);
        return $lines;
    }

    public static function regionToText($num) {
        if ($num < 0)
            return '-';

        if (!isset($GLOBALS['regions']))
            $GLOBALS['regions'] = self::regions();

        return $GLOBALS['regions'][$num];
    }

    public static function formatDate($date) {
        $date = strtr($date, ['00:00:00' => '']);
        $date = trim($date);

        if (preg_match("#(\d\d)\D(\d\d)\D(\d\d\d\d)#", $date, $m)) {
            return $m[1] . '.' . $m[2] . '.' . $m[3];
        }

        if (preg_match("#(\d\d\d\d)\D(\d\d)\D(\d\d)#", $date, $m)) {
            return $m[3] . '.' . $m[2] . '.' . $m[1];
        }

        return $date;
    }
}
