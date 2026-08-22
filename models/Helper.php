<?php

namespace app\models;

class Helper {

    /**
     * @return array<int, string>
     */
    public static function getCemeteryList() {
        $list = Cemetery::find()->all();
        $result = [];
        foreach ($list as $elem) {
            $result[$elem->id] = $elem->name;
        }

        return $result;
    }

    /**
     * @return array<int, string>
     * @param int $cemetery_id
     */

    public static function getBookList($cemetery_id) {
        $list = Book::find()->andWhere(['cemetery_id' => $cemetery_id])->all();
        $result = [0 => "-"];
        foreach ($list as $elem) {
            $result[$elem->id] = $elem->name;
        }

        return $result;
    }

    /**
     * @return array<string>
     * @param string $filename
     */
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

    /**
     * @return array<string>
     */
    public static function regions() {
        $filepath = "../data/spb_region.txt";
        $lines = self::readFileToList($filepath);
        return $lines;
    }

    /**
     * @return string
     * @param int $num
     */
    public static function regionToText($num) {
        if ($num < 0)
            return '-';

        if (!isset($GLOBALS['regions']))
            $GLOBALS['regions'] = self::regions();

        return $GLOBALS['regions'][$num];
    }

    /**
     * @return string
     * @param string|null $date
     */
    public static function formatDate($date) {
        $date = strtr($date ?? '', ['00:00:00' => '']);
        $date = trim($date);

        if (preg_match("#(\d\d)\D(\d\d)\D(\d\d\d\d)#", $date, $m)) {
            return $m[1] . '.' . $m[2] . '.' . $m[3];
        }

        if (preg_match("#(\d\d\d\d)\D(\d\d)\D(\d\d)#", $date, $m)) {
            return $m[3] . '.' . $m[2] . '.' . $m[1];
        }
	
        return $date;
    }

    /**
     * @return string
     * @param string $text
     * @param string $fontFile
     * @param int $fontSize
     * @param int $width
     * @param \Mpdf\Mpdf $mpdf
     * @param bool $sliceInSpace
     */
    public static function truncateToWidth($text, $fontFile, $fontSize, $width, $mpdf = null, $sliceInSpace = false) {
        $getStringWidthInPixels = function($text, $fontFile, $fontSize, $mpdf) {
            $mpdf->SetFont($fontFile, '', $fontSize);
            return $mpdf->GetStringWidth($text) * $mpdf->dpi / 25.4;
        };

        // Если текст изначально влезает, ничего делать не нужно
        if ($getStringWidthInPixels($text, $fontFile, $fontSize, $mpdf) <= $width) {
            return $text;
        }

        // БИНАРНЫЙ ПОИСК (работает мгновенно даже с огромным текстом)
        $low = 0;
        $high = mb_strlen($text);
        $truncatedText = '';

        while ($low <= $high) {
            $mid = intval(($low + $high) / 2);
            $subText = mb_substr($text, 0, $mid);
            $currentWidth = $getStringWidthInPixels($subText, $fontFile, $fontSize, $mpdf);

            if ($currentWidth <= $width) {
                $truncatedText = $subText; // Запоминаем последний удачный вариант
                $low = $mid + 1;           // Пробуем взять больше символов
            } else {
                $high = $mid - 1;          // Текст слишком широкий, берем меньше
            }
        }

        // ОБРЕЗКА ПО ПРОБЕЛУ
        // Так как мы вошли в этот блок, значит исходный текст ТОЧНО резался
        if ($sliceInSpace === true) {
            $last_space = mb_strrpos($truncatedText, " ");

            if ($last_space !== false && $last_space > 0) {
                $truncatedText = mb_substr($truncatedText, 0, $last_space);
            }
        }

        return $truncatedText;
    }

    /**
     * @return void
     * @param string $labelText
     * @param string $data
     * @param int $firstSize
     * @param int $fullSize
     * @param int $fontSize
     * @param string $fontFile
     * @param \Mpdf\Mpdf $mpdf
     * @param int $spaceRepeat
     */
    public static function tablePrint($labelText, $data, $firstSize, $fullSize, $fontSize, $fontFile, $mpdf, $spaceRepeat = 20) {
        echo "<table><tr>";

        if($labelText !== ''){
            echo '<td class="table_label">' . $labelText . '</td>';
        }

        $string = str_repeat("\u{00A0}", $spaceRepeat) . $data;
        $first = true;

        while ($string !== ''){                      
            if ($first){
                $temp_str = self::truncateToWidth($string, $fontFile, $fontSize, $firstSize, $mpdf, true);
                $string = mb_substr($string, mb_strlen($temp_str));
                echo '<td class="table_data">' . $temp_str . '</td></tr>';
            }
            else {
                $temp_str = self::truncateToWidth($string, $fontFile, $fontSize, $fullSize, $mpdf, true);
                $string = mb_substr($string, mb_strlen($temp_str));
                $colspan = ($labelText !== '') ? ' colspan="2"' : '';
                echo '<tr><td' . $colspan . ' class="table_data">' . $temp_str . '</td></tr>';
            }

            $first = false;
        }

        echo "</table>";
    }
}
