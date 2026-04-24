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

class DebugParseController extends Controller {

    public function actionIndex($search = 0) {
    	if($search){
    		$this->actionSeach();
    		exit;
    	}
        exit;
    	$sum = 0;
        $cemeteries = Cemetery::find()->orderBy('id desc')->all();
        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;

            $table_name = "__search_form_$c_id";


            $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

            $sum+=\app\models\SearchFormBasic::find()->count();


        }

        echo $sum;exit;

    	echo 32;exit;
     	$filepath = "oneparse.csv";
        $csv = new \ParseCsv\Csv();
//$csv->offset = 1;
        $csv->delimiter = ",";
        $cemeteries = [];
        $csv->parseFile($filepath);

        echo sizeof($csv->data);



        $iindex = 0;
        $not_found_cnt = 0;
        foreach ($csv->data as $row) {
            $iindex++;
			$row = array_values($row);
         	$record = Record::find()
         		->andWhere(['fio'=>$row[2]])
         		->andWhere(['docnum'=>$row[6]])
         		->andWhere(['age'=>$row[3]])
         		->andWhere(['zags'=>$row[7]])//7
         		->andWhere("book_id > 2000")
         		->one();
         	if(!$record){
         		print_r($row);
         		file_put_contents("debug2.txt", implode(";", $row) . "\r\n", FILE_APPEND);
         		//exit;
         	}
		}


    }

    public function actionSeach(){
        $cemeteries = Cemetery::find()->orderBy('id desc')->all();

        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;
            $books = Book::find()
                    ->andWhere(['cemetery_id' => $cemetery->id])
                    ->all();

            $table_name = "__search_form_$c_id";
            $GLOBALS['search_form_table'] =  $table_name ;

            foreach ($books as $book) {
            	$records = \app\models\Record::find()
                        ->andWhere(['book_id' => $book->id])
                        ->orderBy('id')
                        ->all();

                foreach($records as $record){
                     $sfb = \app\models\SearchFormBasic::find()
                	->andWhere(['record_id'=>$record->id])
                	->one();

                	if(!$sfb){
                		print_r($record);
                		echo "@$c_id@".$book->id.'@'.$record->id;
                		exit;
                	}

                }
            }
    }

}

}