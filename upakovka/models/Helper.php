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
}
