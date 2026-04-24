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

        $book->save();
        return $book;
    }

    public function findFile($filename, $cemetery, $svazka, $kniga) {
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

        echo $filename . ';' . $folder . ';';

        $result = [];

        foreach ($this->_dermo as $dermo) {
            if (!substr_count($dermo, $folder)) {
                continue;
            }
            if (!substr_count($dermo, $filename)) {
                continue;
            }

            $dsvazka = false;
            $dkniga = false;

            if (preg_match("#.*?(\d+).*?#", $svazka, $m)) {
                print_r($m);
                $dsvazka = $m[1];
//exit;
            }

            if (preg_match("#.*?(\d+).*?#", $kniga, $m)) {
                print_r($m);
                $dkniga = $m[1];
                //exit;
            }

            if (($dsvazka) && ($dkniga)) {
                if (preg_match("#$dsvazka" . "[^/]*?/[^/]*?$dkniga#", $svazka, $m)) {
                    echo "!!!$dermo!!";
                    exit;
                }
            }

            exit;
            echo $dermo . ';';
        }

        exit;
    }

    public function actionIndex() {
        $myfile = fopen("finddd.txt ", "r") or die("Unable to open file!");
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
        $csv->delimiter = ", ";
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

        foreach ($csv->data as $row) {
            print_r($row);
            //exit;
            $row = array_values($row);
            //$cemeteries[] = $row[12];
            print_r($row);
            exit;

            $numReg = $row[0];

            $record = new Record();

            $filename = $row[15];
            $this->findFile($filename, $row[12], $row[13], $row[14]);
            exit;
            if (intval($numReg) != $numReg) {
                $record->numLiteral = $numReg;
                //print_r($row);
                //exit;
            } else {
                $record->numReg = $numReg;
            }

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

            //$book = $this->getOrCreateBook($cemetery, $svazkaNum, $bookNum);
            //$record = new Record();
            //exit;
        }

        $cemeteries = array_values(array_unique($cemeteries));
        print_r($cemeteries);
        $this->setEmptyBooks();
    }

    public function setEmptyBook(Book $book) {
        $book->records = Record::find()
                ->andWhere(['book_id' => $book->id])
                ->count();

        $book->per_page = 12;

        $records = Record::find()
                ->andWhere(['book_id' => $book->id])
                ->all();

        $statInfo = [
            'year1' => null,
            'year2' => null,
            'records' => 0
        ];
        foreach ($records as $record) {
            if (preg_match('#(\d\d\d\d)#', $v, $m)) {
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

        $book->year1 = $statInfo['year1'];
        $book->year2 = $statInfo['year2'];
        $book->save();
    }

    public function setEmptyBooks() {
        $books = Book::find()
                ->andWhere("id > " . $this->lastBookId)
                ->all();

        foreach ($books as $book) {
            $this->setEmptyBook($book
            );
        }
    }
}
