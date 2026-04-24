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

class OneParseController extends Controller {

    private $lastBookId = 0;
    private $_dermo = [];

    public function genBookName($cemetery, $svazkaNum, $bookNum) {
        $template = $cemetery->name . ", связка $svazkaNum, книга $bookNum";
        return $template;
    }

    public function getOrCreateBook($cemetery, $svazkaNum, $bookNum) {
        $book = Book::find()
                ->andWhere(['cemetery_id' => $cemetery->id])
                ->andWhere(['svazka' => $svazkaNum])
                ->andWhere(['number' => $bookNum])
                ->one();

        if ($book)
            return $book;

        $book = new Book();
        $book->name = $this->genBookName($cemetery, $svazkaNum, $bookNum);
        $book->cemetery_id = $cemetery->id;
        $book->svazka = $svazkaNum;
        $book->number = $bookNum;
        $book->status = 6;
        $book->records = '1';

        $book->save();
        //print_r($book);
        //exit;
        return $book;
    }

    public function findFile($filename, $cemetery, $svazka, $kniga, $row) {
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

        if (!isset($cemetery_folder[$cemetery])) {
            return false;
        }

        $folder = $cemetery_folder[$cemetery];

//echo $filename . ';' . $folder . ';';

        $result = [];

        foreach ($this->_dermo as $dermo) {

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
                //print_r($m);
                //echo $dermo . ',';
                $dsvazka_dermo = $m[1];
                //exit;
            }

            if (preg_match("#.*?/Кн\.\s*([^/]*?)/.*?#", $dermo, $m)) {
                //print_r($m);
                //echo $dermo . ';';
                $dkniga_dermo = $m[1];

                //exit;
            }

            if (substr_count($dkniga_dermo, "-")) {
                //echo $dkniga_dermo;
                //exit;
            }

            /*
              if (!substr_count($dermo, "Св.")) {
              if (!substr_count($dermo, "Кн.")) {
              echo "$dermo!!!!";
              exit;
              continue;
              } else {
              if (preg_match("#.*?\D([\d]+\D?)/[^/]*?\.jpe?g.*?#", $dermo, $m)) {
              //print_r($m);
              $dkniga_dermo = $m[1];
              $dsvazka_dermo = false;
              } else {
              continue;
              }
              //echo $dermo;
              //exit;
              }
              } else {

              }
             */


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
                //$d1 = "#.*?[^\d]$dsvazka_excel" . "/[^\d]*?$dkniga_excel" . "/.*?#";
                //if (preg_match($d1, $dermo, $m)) {
                //    return $dermo;
                //}
            }

            if ((!$dsvazka_excel) && (!$dkniga_excel)) {
                if ((!$dsvazka_dermo) && (!$dkniga_dermo)) {
                    return $dermo;

                }

                echo $dermo . ",dermo";
                continue;
            }

            if ((!$dsvazka_excel) && ($dkniga_excel)) {
                if ($dsvazka_dermo) {
                    echo "svazka $dsvazka_dermo";
                    print_r($row);
                    //exit;
                    continue;
                }

                echo "what?";
                print_r($row);
                echo $dermo;
                //exit;

                if ((!$dsvazka_dermo) && ($dkniga_excel == $dkniga_dermo)) {
                    //echo $dermo . ',,';
                    //exit;
                    return $dermo;
                }

                /*
                  $d1 = "#.*?[^\d]$dsvazka_e" . "/[^\d]*?$dkniga_excel" . "/.*?#";
                  if (preg_match($d1, $dermo, $m)) {
                  return $dermo;
                  }

                 */
            }

//exit;
//echo $dermo . ';';
        }

        // print_r($paths);
        // exit;

        return false;
    }

    public function findFile2($filename, $cemetery, $svazka, $kniga) {
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
            "БОЛЬШЕОХТИНСКОЕ" => "БОЛЬШЕОХТИНСКОЕ"
        ];

        if (!isset($cemetery_folder[$cemetery])) {
            return false;
        }

        $folder = $cemetery_folder[$cemetery];

//echo $filename . ';' . $folder . ';';

        $result = [];

        foreach ($this->_dermo as $dermo) {
            if (!substr_count($dermo, $folder)) {
                continue;
            }
            if (!substr_count($dermo, $filename)) {
                continue;
            }

            //echo $dermo . ";\n";

            $dsvazka = false;
            $dkniga = false;

            if (preg_match("#.*?(\d+).*?#", $svazka, $m)) {
//print_r($m);
                $dsvazka = $svazka;
//exit;
            }

            if (preg_match("#.*?(\d+).*?#", $kniga, $m)) {
//print_r($m);
                $dkniga = $kniga;
//exit;
            }

            if (($dsvazka) && ($dkniga)) {
                $d1 = "#.*?[^\d]$dsvazka" . "/[^\d]*?$dkniga" . "/.*?#";
//echo $d1;
//echo $dermo;
                if (preg_match($d1, $dermo, $m)) {
                    return $dermo;
//exit;
                }
            }

            if ((!$dsvazka) && (!$dkniga)) {
                echo $dermo . ",";
            }

//exit;
//echo $dermo . ';';
        }

        return false;
    }

    public function actionIndex() {
        //$this->lastBookId=2000;
        //$this->setEmptyBooks();
        //exit;


        $myfile = fopen("finddd.txt", "r") or die("Unable to open file!");
        while (!feof($myfile)) {
            $line = fgets($myfile);
            $line = trim($line);
            $this->_dermo[] = $line;
        }
        fclose($myfile);

//print_r($this->_dermo);
//exit(0);

        $filepath = "oneparse.csv"; 
        $csv = new \ParseCsv\Csv();
//$csv->offset = 1;
        $csv->delimiter = ",";
        $cemeteries = [];
        $csv->parseFile($filepath);

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

        $cemetery_values = [];

        foreach ($cemetery_ids as $cName => $cId) {
            $cemetery = Cemetery::find()
                    ->andWhere(['id' => $cId])
                    ->one();

            $cemetery_values[$cName] = $cemetery;
        }

        $book = Book::find()
                ->orderBy("id desc")
                ->one();

        $this->lastBookId = $book->id;

        $iindex = 0;
        $not_found_cnt = 0;
        foreach ($csv->data as $row) {
            $iindex++;
//print_r($row);
//exit;
            $row = array_values($row);
            //print_r($row);exit;
            $cemeteries[] = $row[12];
//print_r($row);
//exit;

            $numReg = $row[0];

            $filename = $row[15];

            $ff = explode(",",$filename);

            $filename = trim($ff[0]);

            $fname = $this->findFile($filename, $row[12], $row[13], $row[14], $row);

            if (!$fname) {
                echo "404 not $iindex found";
                print_r($row);
                //$this->findFile2($filename, $row[12], $row[13], $row[14]);
                $not_found_cnt++;
                if ($not_found_cnt > 1000000)
                    exit;
                //exit;
                file_put_contents("debug.txt", implode(";", $row) . "\r\n", FILE_APPEND);
                //continue;
            } else {
                $fname = strtr($fname, ['./' => 'raznoe/']);
            }
            //continue;

            $record = new Record();

            if ((!preg_match("#^(\d+)$#",$numReg))||(intval($numReg) != $numReg)) {
                $record->numLiteral = $numReg;
//print_r($row);
//exit;
            } else {
                //echo (intval($numReg) == $numReg);exit;
                $record->numReg = $numReg;
            }

            if ($fname)
                $record->filename = $fname;

            if(sizeof($ff)>1) $record->filename2 = trim($ff[1]);

            $record->fio = $row[2];
            $record->age = $row[3];
            $record->death_date = $row[4];
            $record->rip_date = $row[5];
            $record->docnum = $row[6];
            $record->zags = $row[7];
            $record->area_num = $row[8];
            $record->row_num = $row[9];
            $record->rip_num = $row[10];
            $record->rip_style = (mb_strtolower(trim($row[1]), 'utf8') == "гроб") ? 1 : 2;
/////
            $record->relative_fio = $row[11];
////////////////
            $cemetery_name = $row[12];
            $cemetery = $cemetery_values[$cemetery_name];

            if (intval($row[13]) != $row[13]) {
//print_r($row);
//echo $row[13] . ';';
//exit;
            }

            if (intval($row[14]) != $row[14]) {
//print_r($row);
//exit;
//echo $row[14] . ';';
            }
            $svazkaNum = $row[13];
            $bookNum = $row[14];
            //continue;
            $book = $this->getOrCreateBook($cemetery, $svazkaNum, $bookNum);
            $record->book_id = $book->id;
            $record->save();
            //print_r($record);
            unset($book);
            unset($record);
            continue;
            //print_r($record);
            //exit;
//$record = new Record();
//exit;
        }

        echo 'done!';
        exit;

        $cemeteries = array_values(array_unique($cemeteries));
        print_r($cemeteries);
        $this->setEmptyBooks();
    }

    public function setEmptyBook(Book $book) {
        //$book->records = Record::find()
        //        ->andWhere(['book_id' => $book->id])
        //        ->count();

        $book->per_page = 12;

        $records = Record::find()
                ->andWhere(['book_id' => $book->id])
                ->all();


        $book->records = sizeof($records).'';

        $statInfo = [
            'year1' => null,
            'year2' => null,
            'records' => 0
        ];
        foreach ($records as $record) {
            $v = $record->rip_date;
 if (!preg_match('#.*?(\d\d\d\d).*?#', $v, $m)) {
    $v =  $record->death_date;
 }
            if (preg_match('#.*?(\d\d\d\d).*?#', $v, $m)) {
            //echo 11221;
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
        }

        $book->year1 = $statInfo['year1'].'';
        $book->year2 = $statInfo['year2'].'';
        //print_r($statInfo);exit;
        $book->save();
         //print_r($book);exit;
    }

    public function setEmptyBooks() {
        $books = Book::find()
                ->andWhere("id > " . $this->lastBookId)
                ->all();

        foreach ($books as $book) {
            $this->setEmptyBook($book);
        }
    }
}