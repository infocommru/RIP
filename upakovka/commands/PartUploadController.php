<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Book;
use app\models\Part;
use app\models\Helper;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\PartUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;

class PartUploadController extends Controller {

    public function getBooksInfo($upload) {
        $books = Book::find()
                ->andWhere(['part_id' => $upload->part_id])
                ->all();
        $infodata = [];
        $f = fopen($upload->id . '/info.txt', 'r');
        if ($f) {
            while (($line = fgets($f)) !== false) {
                $line = trim($line);
                $ll = explode("=", $line);
                if (sizeof($ll) < 2)
                    continue;
                $infodata[intval($ll[0])] = $ll[1];
            }
            fclose($f);
        }

        $result = [];

        foreach ($infodata as $index => $filepath) {
            $result[$index] = ['filepath' => $filepath, 'index' => $index];
            foreach ($books as $book) {
                if (substr_count($filepath, $book->name)) {
                    $result[$index]['book'] = $book;
                    break;
                }
            }
        }

        return $result;
    }

    public function checkBooks($upload, $info) {
        $books = Book::find()
                ->andWhere(['part_id' => $upload->part_id])
                ->all();

        if (sizeof($books) != sizeof($info)) {
            return false;
        }


        foreach ($books as $book) {
            $find = false;
            foreach ($info as $index => $bInfo) {
                if (substr_count($bInfo['filepath'], $book->name)) {
                    $find = true;
                    break;
                }
            }

            if (!$find)
                return false;
        }

        return true;
    }

    public function reupload($upload, $bookdata) {
        $book = $bookdata['book'];
        $index = $bookdata['index'];
        $bId = $book->id;
        $sql = <<<"HERE"
           delete from record  where book_id = $bId;            
HERE;

        echo $sql . "\n";

        $r = \Yii::$app->getDb()->createCommand($sql)->execute();

        echo $r . "\n";

        $statInfo = HelperCsv::processBookCsv($book->id, $upload->id . "/$index.csv");

        $book->year1 = $statInfo['year1'] . '';
        $book->year2 = $statInfo['year2'] . '';
        $book->records = $statInfo['records'] . '';
        $book->per_page = $statInfo['per_page'];

        print_r($statInfo);
        //exit;

        $book->save();
    }

    public function processUpload($upload) {
        $cwd = getcwd();
        chdir("./web/upload/part");
        exec("rm -rf  " . $upload->id);
        //echo "unzip me!\n";
        //sleep(60);
        exec("unzip " . $upload->id . ".zip -d $upload->id");
        //echo "unzipped\n";
        exec('python3 ../../temp/upload_part.py ' . $upload->id);

        $booksInfo = $this->getBooksInfo($upload);
        if (!$this->checkBooks($upload, $booksInfo)) {
            $upload->status = 2;
            $upload->save();
            return false;
        }

        $pId = $upload->part_id;
        $sql = <<<"HERE"
           delete from part_record  where part_id = $pId;            
HERE;

        $r = \Yii::$app->getDb()->createCommand($sql)->execute();
        foreach ($booksInfo as $bookdata) {
            $this->reupload($upload, $bookdata);
        }


        return true;
    }

    public function actionIndex() {
        $uploads = PartUpload::find()
                ->andWhere(['status' => 0])
                ->orderBy('id')
                ->all();

        foreach ($uploads as $upload) {
            $upload->status = 1;
            $upload->save();
            if ($this->processUpload($upload)) {
                $upload->status = 3;
                $upload->save();
            }

            $part = Part::find()
                    ->andWhere(['id' => $upload->part_id])
                    ->one();

            $part->update_flag = 1;
            $part->save();

            //print_r($upload);
        }

        HelperLevoshkin::setPartRecords();
    }
}
