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

class TempKazController extends Controller {

    public function update_1923() {
        $cemetery_id = 17;
        $book = Book::find()
                ->andWhere(['cemetery_id' => $cemetery_id])
                ->andWhere(['number' => 183])
                ->andWhere(['svazka' => 32])
                ->one();

        $book->year1 = 2023;
        $book->year2 = 2023;
        $book->save();

        $records = \app\models\Record::find()
                ->andWhere(['book_id' => $book->id])
                ->all();

        foreach ($records as $record) {
            $record->death_date = strtr($record->death_date, ['1923' => '2023']);
            $record->rip_date = strtr($record->rip_date, ['1923' => '2023']);
            $record->save();
        }
    }

    public function actionIndex() {
        $this->update_1923();
        exit;

        $cemetery_kaz_id = 20;
        $cemetery_new_name = 'Казанское кладбище (СПБ)';

        $cemetery = Cemetery::find()->andWhere(['id' => $cemetery_kaz_id])->one();
        //if ($cemetery->name == $cemetery_new_name)
        //    exit;

        $cemetery->name = $cemetery_new_name;
        $cemetery->save();

        $cemetery_pushkin = new Cemetery();
        $cemetery_pushkin->name = 'Казанское кладбище (Пушкин)';
        $cemetery_pushkin->save();

        $valid_list = [
            13 => [87, 88],
            14 => [91],
            15 => [95, 94],
            16 => [104, 105],
            17 => [110],
            18 => [111, 116],
            19 => [117, 120, 123],
            21 => [132, 133],
            22 => [138, 141],
        ];

        $books = Book::find()->andWhere(['cemetery_id' => $cemetery_kaz_id])->all();
        foreach ($books as $book) {
            $sv = intval($book->svazka);
            $nm = intval($book->number);

            $valid = false;
            if (isset($valid_list[$sv])) {
                if (in_array($nm, $valid_list[$sv])) {
                    $valid = true;
                    //echo $book->name . "\r\n";
                }
            }

            if (!$valid) {
                echo $book->name . "\r\n";
                $book->cemetery_id = $cemetery_pushkin->id;
                $book->save();
            }
        }
    }
}
