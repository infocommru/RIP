<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Record;
use app\models\Book;
use app\models\Part;
use app\models\Helper;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;

class ZagsRawController extends Controller {

    public $error_limit = 85;

    public function actionIndex() {

        //$zags_list = Helper::regions();
        $zagsList = \app\models\Zags::find()->andWhere(['deleted' => 0])->orderBy('id')->all();
        $zagsRawList = \app\models\ZagsRaw::find()->asArray()->all();
        //Record::updateAll(['zags_id' => null]);
        
        foreach ($zagsRawList as $raw) {
            $zname = trim($raw['name']);
            if (!$zname)
                continue;

            $pmax = 0;
            $idMax = -1;
            foreach ($zagsList as $zags) {
                $zname2 = $zags->name;
                similar_text($zname, $zname2, $p);
                if ($p > $pmax) {
                    $pmax = $p;
                    $idMax = $zags->id;
                }
            }

            $zraw = \app\models\ZagsRaw::find()->andWhere(['id' => $raw['id']])->one();
            $zraw->percent = round($pmax);
            $zraw->zags_simular_id = $idMax;

            if ($zraw->percent >= $this->error_limit) {
                $zraw->zags_id = $idMax;
                Record::updateAll(['zags_id' => $idMax], ['zags' => $raw['name']]);
            }

            $zraw->save();

            //print_r($zraw);
            //exit;
        }

        echo sizeof($zagsRawList);
    }
}
