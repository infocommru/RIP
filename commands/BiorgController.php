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

class BiorgController extends Controller {

    public $_images = [];

    public function findFile($filename, $cemetery_name, $svazka, $kniga, $row) {
        if (!$this->_images) {
            $myfile = fopen("finddd.txt", "r") or die("Unable to open file!");
            while (!feof($myfile)) {
                $line = fgets($myfile);
                $line = trim($line);
                //if(!in_array($line,$this->_images))
                $this->_images[] = $line;
            }
            fclose($myfile);

            $this->_images = array_values(array_unique($this->_images));
        }

        $cemetery_ids = [
            "Богословское" => 25,
            "Большеохтинское" => 27,
            "БОЛЬШЕОХТИНСКОЕ" => 27,
            "Киновеевское" => 19,
            "КИНОВЕЕВСКОЕ" => 19,
            "Казанское" => 20,
            "Лахтинское" => 15,
            "Мало-охтинское" => 28,
        ];

        $cemetery_folder = [
            "Богословское" => "Богословское",
            "Большеохтинское" => "БОЛЬШЕОХТИНСКОЕ",
            "БОЛЬШЕОХТИНСКОЕ" => "БОЛЬШЕОХТИНСКОЕ",
            "Киновеевское" => "Киновеевское+Казанское",
            "КИНОВЕЕВСКОЕ" => "Киновеевское+Казанское",
            "Казанское" => "Киновеевское+Казанское",
            "Лахтинское" => "Лахтинское",
            "Мало-охтинское" => "Мало-охтинское кладбище",
        ];

        if (!isset($cemetery_folder[$cemetery_name])) {
            return false;
        }

        $folder = $cemetery_folder[$cemetery_name];

        foreach ($this->_images as $dermo) {
            if (!substr_count($dermo, $folder)) {
                continue;
            }
            if (!substr_count($dermo, $filename)) {
                continue;
            }

            $dsvazka_excel = false;
            $dkniga_excel = false;

            $dsvazka_dermo = false;
            $dkniga_dermo = false;

            if (preg_match("#.*?(\d+).*?#", $svazka, $m)) {
                $dsvazka_excel = $svazka;
            }

            if (preg_match("#.*?(\d+).*?#", $kniga, $m)) {
                $dkniga_excel = $kniga;
            }


            if (preg_match("#.*?/Св\.\s*([^/]*?)/.*?#", $dermo, $m)) {
                $dsvazka_dermo = $m[1];
            }

            if (preg_match("#.*?/Кн\.\s*([^/]*?)/.*?#", $dermo, $m)) {
                $dkniga_dermo = $m[1];
            }




            if (($dsvazka_excel) && ($dkniga_excel)) {
                if ($dsvazka_excel != $dsvazka_dermo) {
                    continue;
                }

                if ($dkniga_excel == $dkniga_dermo) {
                    return $dermo;
                }

                $d12 = explode("-", $dkniga_dermo);
                if (intval($dkniga_excel)) {
                    if (sizeof($d12) > 1) {
                        //print_r($row);
                        //echo $dermo;
                        $d1 = intval($d12[0]);
                        $d2 = intval($d12[1]);
                        if ((intval($dkniga_excel) >= $d1) && (intval($dkniga_excel) <= $d2)) {
                            return $dermo;
                        }
                    }
                }
                continue;
            }

            if ((!$dsvazka_excel) && (!$dkniga_excel)) {
                if ((!$dsvazka_dermo) && (!$dkniga_dermo)) {
                    return $dermo;
                }

                //echo $dermo . ",dermo";
                //continue;
            }

            if ((!$dsvazka_excel) && ($dkniga_excel)) {
                if ($dsvazka_dermo) {
                    //echo "svazka $dsvazka_dermo";
                    //print_r($row);
                    //exit;
                    continue;
                }

                //echo "what?";
                //print_r($row);
                //echo $dermo;
                //exit;

                if ((!$dsvazka_dermo) && ($dkniga_excel == $dkniga_dermo)) {
                    //echo $dermo . ',,';
                    //exit;
                    return $dermo;
                }
            }
        }


        return false;
    }

    public function getBook($cemeteryId, $svazka, $kniga) {
        $book2 = Book::find()
                ->andWhere(['cemetery_id' => $cemeteryId])
                ->andWhere(['number' => $kniga])
                ->andWhere(['svazka' => $svazka])
                ->one();

        //echo 12345;
        //exit;

        if (!$book2) {
            $book2 = new Book();
            $book2->cemetery_id = $cemeteryId;
            $book2->number = $kniga;
            $book2->svazka = $svazka;

            $cemetery = Cemetery::find()
                    ->andWhere(['id' => $cemeteryId])
                    ->one();

            $name = $cemetery->name;
            $name .= " , Св. $svazka, Кн. $kniga";

            $book2->name = $name;
            $book2->records = "1";
            $book2->year1 = "1000";
            $book2->year2 = "1000";
            $book2->per_page = 12;
            $book2->save();
        }

        //echo 2111;
        //exit;

        return $book2;
    }

    public function updateRecord($record, $one, $iteration, $filepath) {
        $record->fio = $one['fio'];
        $record->age = $one['age'];
        $record->death_date = $one['death_date'];
        $record->rip_date = $one['rip_date'];
        $record->docnum = $one['docnum'];
        $record->zags = $one['zags'];
        $record->relative_fio = $one['relative_fio'];
        $record->area_num = $one['area_num'];
        $record->row_num = $one['row_num'];
        $record->rip_num = $one['rip_num'];

        if (!$filepath) {
            $values = array_values($one);
            file_put_contents("not_found$iteration.txt", implode(";", $values) . "\r\n", FILE_APPEND);
        } else {
            $record->filename = $filepath;
             $values = array_values($one);
            file_put_contents("found$iteration.txt", implode(";", $values) . "\r\n", FILE_APPEND);
        }

        $record->save();
    }

    public function biorg1() {
        $filepath = "./temp/biorg1.csv";
        $csv = new \ParseCsv\Csv();

        $csv->delimiter = ",";
        $cemeteries = [];
        $csv->parseFile($filepath);
        foreach ($csv->data as $row) {
            $row = array_values($row);
            //print_r($row);
            //exit;
            $one = [
                'regnum' => $row[1],
                'fio' => $row[3],
                'age' => $row[4],
                'death_date' => $row[5],
                'rip_date' => $row[6],
                'docnum' => $row[7],
                'zags' => $row[8],
                'area_num' => $row[9],
                'row_num' => $row[10],
                'rip_num' => $row[11],
                'relative_fio' => $row[12],
                'svazka' => $row[14],
                'kniga' => $row[15],
                'filename' => $row[16],
                'comment' => $row[17],
                'rip_style' => ($row[2] == 'гроб' ? 1 : 2),
            ];

            $records = Record::find()
                    ->andWhere(['fio' => $one['fio']])
                    ->andWhere(['docnum' => $one['docnum']])
                    ->all();

            $s = sizeof($records);
            //echo $s . ';';
            if ($s != 1) {
                continue;
            }
            
            
            
            $record = $records[0];
            $book = Book::find()->andWhere(['id' => $record->book_id])->one();
            $book2 = false;

            if (($book->number != $one['kniga']) || ($book->svazka != $one['svazka'])) {
                $book2 = $this->getBook(25, $one['svazka'], $one['kniga']);
                $record->book_id = $book2->id;
            }


            $f = $this->findFile($one['filename'], "Богословское", $one['svazka'], $one['kniga'], $one);

            if($f)$f = strtr($f,["./"=>"raznoe/"]);
            //echo $f;exit;

            $this->updateRecord($record, $one, 1, $f);

            //print_r($one);
            //echo $f;
            //exit;
        }
    }

    public function biorg2() {
        $filepath = "./temp/biorg2.csv";
        $csv = new \ParseCsv\Csv();

        $csv->delimiter = ",";
        $cemeteries = [];
        $csv->parseFile($filepath);
        foreach ($csv->data as $row) {
            $row = array_values($row);
            $one = [
                'regnum' => $row[1],
                'fio' => $row[3],
                'age' => $row[4],
                'death_date' => $row[5],
                'rip_date' => $row[6],
                'docnum' => $row[7],
                'zags' => $row[8],
                'area_num' => $row[9],
                'row_num' => $row[10],
                'rip_num' => $row[11],
                'relative_fio' => $row[12],
                'svazka' => $row[14],
                'kniga' => $row[15],
                'filename' => $row[16],
                'comment' => $row[17],
                'rip_style' => ($row[2] == 'гроб' ? 1 : 2),
            ];

            $records = Record::find()
                    ->andWhere(['fio' => $one['fio']])
                    ->andWhere(['docnum' => $one['docnum']])
                    ->andWhere(['age' => $one['age']])
                    ->all();

            $s = sizeof($records);

            if ($s != 1) {
                //print_r($one);
                continue;
            }

            $record = $records[0];
            $book = Book::find()->andWhere(['id' => $record->book_id])->one();
            $book2 = false;

            if (($book->number != $one['kniga']) || ($book->svazka != $one['svazka'])) {
                $book2 = $this->getBook(27, $one['svazka'], $one['kniga']);
                $record->book_id = $book2->id;
            }

            $f = $this->findFile($one['filename'], "Большеохтинское", $one['svazka'], $one['kniga'], $one);

            if($f)$f = strtr($f,["./"=>"raznoe/"]);


            $this->updateRecord($record, $one, 2, $f);
        }
    }

    public function biorg3() {
        $filepath = "./temp/biorg3.csv";
        $csv = new \ParseCsv\Csv();

        $csv->delimiter = ",";
        $cemeteries = [];
        $csv->parseFile($filepath);
        foreach ($csv->data as $row) {
            $row = array_values($row);
            //print_r($row);exit;

            $one = [
                'regnum' => $row[0],
                'fio' => $row[2],
                'age' => $row[3],
                'death_date' => $row[4],
                'rip_date' => $row[5],
                'docnum' => $row[6],
                'zags' => $row[7],
                'area_num' => $row[8],
                'row_num' => $row[9],
                'rip_num' => $row[10],
                'relative_fio' => $row[11],
                'svazka' => $row[13],
                'kniga' => $row[14],
                'filename' => $row[15],
                'comment' => $row[16],
                'rip_style' => ($row[1] == 'гроб' ? 1 : 2),
            ];

            $books = Book::find()
                    ->andWhere(['cemetery_id' => 19])
                    ->all();

            $bookIds = [];

            foreach ($books as $book) {
                $bookIds[] = $book->id;
            }

            $records = Record::find()
                    ->andWhere(['fio' => $one['fio']])
                    ->andWhere(['age' => $one['age']])
                    ->andWhere(['docnum' => $one['docnum']])
                    ->andWhere(['in', 'book_id', $bookIds])
                    ->all();

            $s = sizeof($records);

            if ($s != 1) {
                //print_r($one);
                continue; //exit;
            }

            $record = $records[0];
            $book = Book::find()->andWhere(['id' => $record->book_id])->one();
            $book2 = false;

            if (($book->number != $one['kniga']) || ($book->svazka != $one['svazka'])) {
                $book2 = $this->getBook(19, $one['svazka'], $one['kniga']);
                $record->book_id = $book2->id;
            }

            $f = $this->findFile($one['filename'], "Киновеевское", $one['svazka'], $one['kniga'], $one);

            if($f)$f = strtr($f,["./"=>"raznoe/"]);


            $this->updateRecord($record, $one, 3, $f);
        }
    }

    public function biorg4() {
        $filepath = "./temp/biorg4.csv";
        $csv = new \ParseCsv\Csv();

        $csv->delimiter = ",";
        $cemeteries = [];
        $csv->parseFile($filepath);
        foreach ($csv->data as $row) {
            $row = array_values($row);
            //print_r($row);exit;

            $one = [
                'regnum' => $row[0],
                'fio' => $row[2],
                'age' => $row[3],
                'death_date' => $row[4],
                'rip_date' => $row[5],
                'docnum' => $row[6],
                'zags' => $row[7],
                'area_num' => $row[8],
                'row_num' => $row[9],
                'rip_num' => $row[10],
                'relative_fio' => $row[11],
                'svazka' => $row[13],
                'kniga' => $row[14],
                'filename' => $row[15],
                'comment' => $row[16],
                'rip_style' => ($row[1] == 'гроб' ? 1 : 2),
            ];

            $books = Book::find()
                    ->andWhere(['cemetery_id' => 20])
                    ->all();

            $bookIds = [];

            foreach ($books as $book) {
                $bookIds[] = $book->id;
            }

            $records = Record::find()
                    ->andWhere(['fio' => $one['fio']])
                    ->andWhere(['age' => $one['age']])
                    ->andWhere(['docnum' => $one['docnum']])
                    ->andWhere(['in', 'book_id', $bookIds])
                    ->all();

            $s = sizeof($records);

            if ($s != 1) {
                //echo 11111;
                //print_r($one);
                continue; //exit;
            }

            $record = $records[0];
            $book = Book::find()->andWhere(['id' => $record->book_id])->one();
            $book2 = false;

            if (($book->number != $one['kniga']) || ($book->svazka != $one['svazka'])) {
                $book2 = $this->getBook(20, $one['svazka'], $one['kniga']);
                $record->book_id = $book2->id;
            }


            $f = $this->findFile($one['filename'], "Казанское", $one['svazka'], $one['kniga'], $one);

            if($f)$f = strtr($f,["./"=>"raznoe/"]);


            $this->updateRecord($record, $one, 4, $f);
        }
    }

    public function actionIndex() {
        echo 1;
        $this->biorg1();
        //exit;
        echo 2;
        //$this->biorg2();
        //exit;
        echo 3;
        //$this->biorg3();
        //exit;
        echo 4;
        //$this->biorg4();
        exit;
    }
}
