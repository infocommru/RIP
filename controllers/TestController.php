<?php

namespace app\controllers;

use app\models\User;
use app\models\HelperLevoshkin;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class TestController extends Controller {

	public function actionLev(){
		print_r( HelperLevoshkin::region("Фрунзенский"));

		exit;
	}



}