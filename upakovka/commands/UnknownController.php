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

class UnknownController extends Controller {

    public function actionIndex($cemetery_id = 0) {
        $zags_list = Helper::regions();
        $cemeteries = Cemetery::find()->orderBy('id desc')->all();
        if ($cemetery_id)
            $cemeteries = Cemetery::find()->andWhere(['id' => $cemetery_id])->all();

        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;

            $table_name = "__search_form_$c_id";

            $books = Book::find()
                    ->andWhere(['cemetery_id' => $cemetery->id])
                    ->all();

            $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

            foreach ($books as $book) {
                $neiz_list = ['неизвестн', 'н/м', 'н/ж'];

                foreach ($neiz_list as $neiz) {
                    $records = \app\models\Record::find()
                            ->andWhere(['book_id' => $book->id])
                            ->andWhere(['like', 'fio', $neiz])
                            ->orderBy('id')
                            ->all();

                    $lastPage = 'asdasd';
                    $lastPagePunkt = 1;
                    foreach ($records as $record) {
                        $record->is_unknown = 1;
                        $record->save();
                    }
                }
            }
        }

        //$this->debug_log();
        exit;
    }

    private function debug_log() {
        $record_ids = [];
        $cemeteries = Cemetery::find()->all();
        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;
            $table_name = "__search_form_$c_id";
            $GLOBALS['search_form_table'] = "__search_form_" . $cemetery->id;

            $rSearch = \app\models\SearchFormBasic::find()->all();
            foreach ($rSearch as $search) {
                $record_ids[] = $search->record_id;
            }
        }

        //print_r( $record_ids);

        $result = \Yii::$app->getDb()->createCommand("delete from record2 where 1")->execute();

        $records = \app\models\Record::find()
                ->andWhere(['not in', 'id', $record_ids])
                ->all();

        foreach ($records as $record) {
            $r = new \app\models\Record2();
            $r->attributes = $record->attributes;
            $r->save();
        }

        //echo sizeof($records);exit;
    }
}
