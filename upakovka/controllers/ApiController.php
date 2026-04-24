<?php

namespace app\controllers;

use app\models\Book;
use app\models\Record;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BookController implements the CRUD actions for Book model.
 */
class ApiController extends Controller {

    public function actionLotus($id) {
        $record = Record::find()->andWhere(['id' => $id])->one();

        $result = $record->filename;
        $result = strtr($result, ["/" => "\\"]);
        if (isset($_GET['file'])) {
            $pos = strrpos($result, "\\");
            $result = substr($result, 0, $pos) . "\\" . $_GET['file'];
        }


        //print_r($record);
        return urldecode($result);
        //$model = $this->findModel($id);
        //return 111;
    }

    public function actionSpell($txt){
        exec("python3 -u ./temp/spell.py \"$txt\" 2>&1",$out);
        if($out){
            echo $out[0];
            exit;
        }
       // print_r($out);
    }
}
