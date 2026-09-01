<?php

namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;

class ImageViewerController extends Controller {
    /**
     * @inheritDoc
     */
    public function behaviors() {
        return array_merge(
                parent::behaviors(),
                [
                    'verbs' => [
                        'class' => VerbFilter::className(),
                    ],
                    'access' => [
                        'class' => AccessControl::className(),
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                            ],
                        ],
                    ],
                ]
        );
    }

    /**
     * Открывает по указанному относительному пути скан через OpenSeadragon
     * @param string $path
     * @return string
     */
    public function actionIndex($path): string {
        $this->layout = 'viewer';

        $path = str_replace('\\', '/', Yii::getAlias("@webimages/{$path}"));

        return $this->render('index', [
            'path' => $path,
        ]);
    }
}