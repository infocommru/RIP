<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Book;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;

class UploadController extends Controller {

    private function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
            $files = array_merge(
                    [],
                    ...[$files, $this->rglob($dir . "/" . basename($pattern), $flags)]
            );
        }
        return $files;
    }

    private function getFiles($folder) {
        $dh = opendir($folder);
        $result = [];
        while ($f = readdir($dh)) {
            if ($f == '..')
                continue;
            if ($f == '.')
                continue;

            $fpath = $folder . '/' . $f;

            if (is_dir(($fpath))) {
                $result = array_merge($result, $this->getFiles($fpath));
            } else {
                $result[] = $fpath;
            }
        }

        closedir($dh);

        return $result;
    }

    public function getBooksInfo($upload) {
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

        return $infodata;
    }

    public function getBookinfo($upload, $index, $bookline) {

        $result = ['number' => "", 'svazka' => ''];

        $book = (explode("/", strtr($bookline, ["\\" => '/'])));
        $book = end($book);
        $book = strtr($book, ['.xlsx' => '']);

        $result['name'] = $book;
        if (preg_match("#.*?(\d+).*?(\d+).*?#", $book, $match)) {

            $result['number'] = $match[2];
            $result['svazka'] = $match[1];
        }

        $data = trim(file_get_contents($upload->id . '/' . $index . '.csv'));

        $result['records'] = substr_count($data, "\n") - 1;

        //print_r($result);exit;
        return $result;
    }

    public function processUpload($upload) {
        $cwd = getcwd();
        chdir("./web/upload/book");
        exec("rm -rf  " . $upload->id);
        //echo "unzip me!\n";
        //sleep(60);
        exec("unzip " . $upload->id . ".zip -d $upload->id");
        //echo "unzipped\n";
        exec('python3 ../../temp/upload.py ' . $upload->id);

        $bInfo = $this->getBooksInfo($upload);
        foreach ($bInfo as $index => $bookline) {
            $bookData = $this->getBookinfo($upload, $index, $bookline);
            $book = new Book();
            $book->cemetery_id = $upload->cemetery_id;
            $book->name = $bookData['name'];

            $bookLast = Book::find()
                ->andWhere(['cemetery_id'=>$upload->cemetery_id])
                ->andWhere(['name'=>$bookData['name']])
                ->one();

            if($bookLast)$book = $bookLast;

            $book->number = $bookData['number'];
            $book->svazka = $bookData['svazka'];
            $book->records = $bookData['records'] . '';

            //$book->year1 = "";
            //$book->year2 = "";
            $book->save();

            $statInfo = HelperCsv::processBookCsv($book->id, $upload->id . "/$index.csv");

            $book->year1 = $statInfo['year1'] . '';
            $book->year2 = $statInfo['year2'] . '';
            $book->records = $statInfo['records'] . '';
            $book->per_page = $statInfo['per_page'];
//print_r($statInfo);exit;

            $book->save();
            if ($upload->part_flag)
                HelperLevoshkin::setBookPart($book);
            //print_r($book);
            //exit;
        }
        //echo $bookline;
        //exit;
        //$files = $this->getFiles($upload->id.'');
        //print_r($files);

        chdir($cwd);
    }

    public function actionIndex() {
        $uploads = BookUpload::find()->andWhere(['status' => 0])->all();
        foreach ($uploads as $upload) {
            $upload->status = 1;
            $upload->save();
            $this->processUpload($upload);
            $upload->status = 2;
            $upload->save();
            //print_r($upload);
        }

        HelperLevoshkin::setPartRecords();
    }
}
