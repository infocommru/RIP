<?php

namespace app\controllers;

use app\models\Book;
use app\models\Record;
use app\models\Cemetery;
use app\models\CacheRecords;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

require_once __DIR__ . '/../vendor/autoload.php';

class PrintController extends Controller {

    /**
     * @inheritDoc
     */

    public function actionIndex($record_id) {
        $record = Record::find()
            ->andWhere(['id' => $record_id])
            ->one();

        $book = Book::find()
            ->andWhere(['id' => $record->book_id])
            ->one();

        $cemetery = Cemetery::find()
            ->andWhere(['id' => $book->cemetery_id])
            ->one();

        $sdata = CacheRecords::find()->query(['term' => ['record_id' => $record_id]])->one();
        $user = \app\models\User::findIdentity(\Yii::$app->user->id);
        return $this->render('index',
            [
                'record' => $record,
                'book' => $book,
                'sdata' => $sdata,
                'user' => $user,
                'cemetery' => $cemetery
            ]
        );
    }

    public function actionNotFoundF2() {
        $user = \app\models\User::findIdentity(\Yii::$app->user->id);

        $params = [];
        if (isset($_GET['params'])) {
            $params = @unserialize(base64_decode($_GET['params']));
        }

        return $this->render('notfoundf2',
            [
                'user' => $user,
                'params' => $params
            ]
        );
    }

    public function actionForma() {
        $fname = "forma_f" . $_GET['spravka'] . '_N_' . $_GET['nn'] . date("_d_m_Y");

        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $pdf = new \Mpdf\Mpdf([
            'fontDir' => array_merge($fontDirs, [
                __DIR__ . '/../assets/fonts',
            ]),
            'fontdata' => array_merge($fontData, [ // lowercase letters only in font key
                'verdana' => [
                    'R' => 'Verdana.ttf',
                    'I' => 'Verdana-Italic.ttf',
                    'B' => 'Verdana-Bold.ttf',
                    'BI' => 'Verdana-BoldItalic.ttf',
                ],
            ]),
            'mode' => 'utf-8', 'format' => 'A5',
            'margin_top' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_bottom' => 0
        ]);

        $pdf->shrink_tables_to_fit = 1;
	    $pdf->SetDisplayMode('default');

        $htmlRender = '';
        $this->layout = false;
        $fname = "forma_f" . $_GET['spravka'] . '_N_' . $_GET['nn'] . date("_d_m_Y");
        
        if ($_GET['spravka'] == '1') {
            $htmlRender = $this->render('form1',);
        } else {
            $htmlRender = $this->render('form2',);
        }

        $pdf->WriteHTML($htmlRender);

        switch ($_GET['saveas']) {
            case '1':
                $pdf->Output($fname . '.pdf', \Mpdf\Output\Destination::INLINE);
                exit;
            case '2':
                $imagick = new \Imagick();
                $result = $pdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
                
                $imagick->setAntiAlias(true);
                $imagick->setOption('pdf:text-antialiasing', '4');
                $imagick->setOption('pdf:graphics-antialiasing', '4');
                $imagick->setResolution(400, 400);
                $imagick->readImageBlob($result . '[0]');
                $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
                $imagick->setImageFormat('jpeg');
                $imagick->scaleImage(1167, 0);

                header('Content-Type: image/jpeg');
                header('Content-Length: ' . strlen($imagick->getImageBlob()));
                echo $imagick->getImageBlob();
                
                $imagick->clear();
                $imagick->destroy();
                exit;
            case '3':
                $pdf->Output($fname . '.pdf', \Mpdf\Output\Destination::DOWNLOAD);
                exit;
            case '4':
                $imagick = new \Imagick();
                $result = $pdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
                
                $imagick->setAntiAlias(true);
                $imagick->setOption('pdf:text-antialiasing', '4');
                $imagick->setOption('pdf:graphics-antialiasing', '4');
                $imagick->setResolution(400, 400);
                $imagick->readImageBlob($result . '[0]');
                $imagick = $imagick->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);
                $imagick->setImageFormat('jpeg');
                $imagick->scaleImage(1167, 0);

                header('Content-Type: image/jpeg');
                header('Content-Length: ' . strlen($imagick->getImageBlob()));
                header('Content-Disposition: attachment; filename=' . $fname . '.jpg');
                echo $imagick->getImageBlob();
                
                $imagick->clear();
                $imagick->destroy();
                exit;
        }
    }
}
