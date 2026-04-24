<?php

namespace app\models;

class HelperImg {

    public static function checkImages($filepath) {
        $filepath = str_replace("\\", "/", $filepath);

        $index_last = strrpos($filepath, "/");
        $folderpath = substr($filepath, 0, $index_last);
        $fname = substr($filepath, $index_last + 1);

        $fullpath = "../upload/rip2/$folderpath";

        $files = glob($fullpath . "/*.*");
        $files = array_map(function ($elem) {
            $index_last = strrpos($elem, "/");
            return $fname = substr($elem, $index_last + 1);
            ;
        }, $files);

        if($files)return true;
        return false;
    }

    public static function findImages($filepath) {
        $filepath = str_replace("\\", "/", $filepath);
        
        $result = $filepath;
        if(self::checkImages($result))return $result;

        $result = strtr($filepath,["Книга" =>'Кн.']);
        if(self::checkImages($result))return $result;

    }

    public static function getImages($filepath) {
        $filepath = str_replace("\\", "/", $filepath);

        $index_last = strrpos($filepath, "/");
        $folderpath = substr($filepath, 0, $index_last);
        $fname = substr($filepath, $index_last + 1);

        $fullpath = "../upload/rip2/$folderpath";

        $files = glob($fullpath . "/*.*");
        $files = array_map(function ($elem) {
            $index_last = strrpos($elem, "/");
            return $fname = substr($elem, $index_last + 1);
            ;
        }, $files);

        $index = array_search($fname, $files);

        $result = [];

        if ($index >= 0) {
            $index_start = $index - 2;
            if ($index_start < 0)
                $index_start = 0;

            $index_end = $index + 5;

            for ($i = $index_start; ($i < $index_end) && ($i < sizeof($files)); $i++) {

                $upath = "/upload/rip2/$folderpath/" . $files[$i];
                $upath2 = str_replace(" ", "%20", $upath);
                $upath22 = $folderpath . "/" . $files[$i];
                $upath3 = $files[$i];

                $small = "/im.php?t=" . $files[$i];

                $result[] = [
                            'url' => $upath2,
                            'src' => $small,
                            'src2' => $upath22,
                            'src3' => $upath3,
                            'options' => array('title' => $files[$i])
                ];
            }

            return $result;
        }


        return [];
    }
}
