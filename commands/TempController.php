<?php

namespace app\commands;

use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Book;
use app\models\Part;
use app\models\Helper;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;

class TempController extends Controller {

    public function actionIndex() {
        //Южное 2
        $cemetery_id =2;
        $svazki = [
            31 =>[172,173,174,175,176,177,178,179],
            32 =>[179,180,181,182,183],
            35 =>[207,208],
            36 =>[220,221],
            37 =>[225,226],
            40 =>[240],
            45 =>[264,265],
        ];

        $books = Book::find()->andWhere(["cemetery_id" => $cemetery_id])->all();
        foreach($books as $book){
            if(isset($svazki[$book->svazka])){
                $numbers=$svazki[$book->svazka];
                if(in_array($book->number,$numbers)){
                    //print_r($book);exit;
                    \app\models\Record::updateAll(['rip_style' => 1,'gos'=>1], ['book_id' => $book->id, ]);
                }
            }
        }


        //Новое колпинское 11
        $cemetery_id =11;
        $svazki = [
            1 =>[1,2,3,4,5,6],
            2 =>[2,3,4,5,6,7,8],
            3 =>[9,10,11,12,13,14,15,16],
            4 =>[17,18],
        ];

        $books = Book::find()->andWhere(["cemetery_id" => $cemetery_id])->all();
        foreach($books as $book){
            if(isset($svazki[$book->svazka])){
                $numbers=$svazki[$book->svazka];
                if(in_array($book->number,$numbers)){
                    //print_r($book);exit;
                 \app\models\Record::updateAll(['rip_style' => 1,'gos'=>1], ['book_id' => $book->id, ]);
                }
            }
        }

        //Ковалевское 6
        $cemetery_id =6;
        $svazki = [
            9 =>[61],
            10 =>[65,66,67],
            12 =>[74,77],
            13 =>[82],
            14 =>[89],
            15 =>[93],
            16 =>[99,102],
            18 =>[109,110],
        ];

        $books = Book::find()->andWhere(["cemetery_id" => $cemetery_id])->all();
        foreach($books as $book){
            if(isset($svazki[$book->svazka])){
                $numbers=$svazki[$book->svazka];
                if(in_array($book->number,$numbers)){
                    //print_r($book);exit;
                    \app\models\Record::updateAll(['rip_style' => 1,'gos'=>1], ['book_id' => $book->id, ]);
                }
            }
        }

        //Северное 4
        $cemetery_id =4;
        $svazki = [
            61 =>[482,490,491],
            63 =>[503,504],
        ];

        $books = Book::find()->andWhere(["cemetery_id" => $cemetery_id])->all();
        foreach($books as $book){
            if(isset($svazki[$book->svazka])){
                $numbers=$svazki[$book->svazka];
                if(in_array($book->number,$numbers)){
                    //print_r($book);exit;
                    \app\models\Record::updateAll(['rip_style' => 1,'gos'=>1], ['book_id' => $book->id, ]);
                }
            }
        }
        exit;

        $ids = [4, 6, 18];
        $cemeteries = Cemetery::find()->andWhere(['in', "id", $ids])->all();
        foreach ($cemeteries as $cemetery) {
            $books = Book::find()->andWhere(["cemetery_id" => $cemetery->id])->all();

            foreach ($books as $book) {
                \app\models\Record::updateAll(['rip_style' => 1], ['book_id' => $book->id, 'is_unknown' => 1]);
            }
        }
    }
}
