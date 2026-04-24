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

class DublController extends Controller {

    private function cmp_record12($record1, $record2) {
        $fields_cmp = [
            'numReg', 'numLiteral', 'age', 'death_date', 'rip_date', 'docnum',
            'fio', 'zags', 'area_num', 'row_num', 'rip_num'
        ];

        foreach ($fields_cmp as $f) {
            if ($record1[$f] != $record2[$f]) {
                return false;
            }
        }

        return true;
    }

    public function actionIndex() {
        $cemeteries = Cemetery::find()->orderBy('id')->all();

        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;

            $books = Book::find()
                    ->andWhere(['cemetery_id' => $cemetery->id])
                    ->all();

            $fields_cmp = [
                'numReg', 'numLiteral', 'age', 'death_date', 'rip_date', 'docnum',
                'fio', 'zags', 'area_num', 'row_num', 'rip_num'
            ];

            foreach ($books as $book) {
                $records = \app\models\Record::find()
                        ->andWhere(['book_id' => $book->id])
                        ->andWhere(['deleted' => 0])
                        ->orderBy('id')
                        ->asArray()
                        ->all();

                for ($i = 0; $i < sizeof($records); $i++) {
                    $record1 = $records[$i];
                    for ($j = $i + 1; $j < sizeof($records); $j++) {
                        $record2 = $records[$j];

                        if ($this->cmp_record12($record1, $record2)) {
                            $r = \app\models\Record::find()
                                    ->andWhere(['id' => $record2['id']])
                                    ->one();

                            $r->dubl = $book->id;
                            $r->save();

                            $r = \app\models\Record::find()
                                    ->andWhere(['id' => $record1['id']])
                                    ->one();

                            $r->dubl = -$book->id;
                            $r->save();
                        }
                    }
                }
            }
        }
    }
}
