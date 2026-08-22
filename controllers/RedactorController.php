<?php

namespace app\controllers;

use app\models\Book;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

class RedactorController extends Controller {

	public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // '@' означает только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
	public function actionIndex(){
        $user = \app\models\User::findIdentity(Yii::$app->user->id);

		return $this->render('index', [
  			'user' => $user,
        ]);
	}


}