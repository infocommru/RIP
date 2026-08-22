<?php

namespace app\commands;

use yii\console\Controller;
use app\models\Book;
use app\models\Cemetery;
use app\models\HelperLevoshkin;

class UnknownController extends Controller {

    /**
     * Creates the search index.
     * @return void
	 * @param int $cemetery_id
     */
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
    }
}
