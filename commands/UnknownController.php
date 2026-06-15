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
use app\models\Record;

class UnknownController extends Controller {

    public function actionIndex($cemetery_id = 0) {
        if ($cemetery_id)
            $cemeteries = Cemetery::find()->andWhere(['id' => $cemetery_id])->all();
        else {
            $cemeteries = Cemetery::find()->orderBy('id desc')->all();
        }

        foreach ($cemeteries as $cemetery) {
            $c_id = $cemetery->id;

            $books = Book::find()
                    ->andWhere(['cemetery_id' => $cemetery->id])
                    ->all();

            foreach ($books as $book) {
                $updated = \app\models\HelperLevoshkin::update_unknown($book);

                if($updated){
                    \app\models\HelperCache::updateCache([$book]);
                }
            }
        }

        exit;
    }
}
