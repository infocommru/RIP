<?php

namespace app\controllers;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;

class RedactorController extends Controller {

	public function actionIndex(){
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

		return $this->render('index', [
  			'user' => $user,
        ]);
	}


}