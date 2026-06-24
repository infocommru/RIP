<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use app\models\Book;
use app\models\Part;
use app\models\Helper;
use app\models\Cemetery;
use app\models\BookUpload;
use app\models\HelperCsv;
use app\models\HelperLevoshkin;
use app\models\CacheRecords;

class SearchCacheController extends Controller {

	private function createIndex() {
		$db = CacheRecords::getDb();
		$command = $db->createCommand();

		if ($command->indexExists(\app\models\CacheRecords::index()))
			$command->deleteIndex(\app\models\CacheRecords::index());

		$standard_type = [
			'type' => 'text', 
			'analyzer' => 'standard', 
			'norms' => false,

			'fields' => [
				'autocomplete' => [
					'type' => 'search_as_you_type'
				],
				'keyword' => [
					'type' => 'keyword',
					'normalizer' => 'lowercase'
				],
				'wildcard' => [
					'type' => 'wildcard',
					"normalizer" => "lowercase"
				],
			]
		];

		$command->createIndex(\app\models\CacheRecords::index(), [
			'settings' => [
				'number_of_shards' => 1,
				'number_of_replicas' => 0,
			],
			'mappings' => [
				'properties' => [
				    'record_id' => ['type' => 'integer'],
					'cemetery_id' => ['type' => 'integer'],

					'regnum' => $standard_type,

					'fam' => $standard_type,
					'nam' => $standard_type,
					'ot' => $standard_type,

					'fio_display' => ['type' => 'keyword', 'index' => 'false'],
					
				    'age_int' => ['type' => 'integer'],
				    'age' => ['type' => 'keyword', 'index' => 'false'],
				    
				    'dead_year' => ['type' => 'integer'],
				    'dead_month' => ['type' => 'integer'],
				    'dead_day' => ['type' => 'integer'],
				    'dead_date' => ['type' => 'keyword', 'index' => 'false', 'fields' => ['date' => [
				    	'type' => 'date', 
				    	'format' => 'dd/MM/yyyy',
				    	'ignore_malformed' => true]]],
				    
				    'rip_year' => ['type' => 'integer'],
				    'rip_month' => ['type' => 'integer'],
				    'rip_day' => ['type' => 'integer'],
				    'rip_date' => ['type' => 'keyword', 'index' => 'false', 'fields' => ['date' => [
				    	'type' => 'date', 
				    	'format' => 'dd/MM/yyyy',
				    	'ignore_malformed' => true]]],
				    
				    'zags' => $standard_type,
				    'rip_style' => ['type' => 'integer'],
				    
				    'unknown' => ['type' => 'integer'],
					'unknown_number' => $standard_type,
				    
				    'docnum' => $standard_type,
					'areanum' => $standard_type,
					'rownum' => $standard_type,
					'ripnum' => $standard_type,

				    'relative' => $standard_type,
				    
				    'svazka_num' => ['type' => 'keyword', 'index' => 'false'],
				    'book_num' => ['type' => 'keyword', 'index' => 'false'],
				    'page_num' => ['type' => 'keyword', 'index' => 'false'],
				    'page_punkt' => ['type' => 'integer', 'index' => 'false'],
				    
				    'comment' => $standard_type,

				    'comment_book' => ['type' => 'keyword', 'index' => 'false'],
				    'book_id' => ['type' => 'integer'],
				    'book_rip_style' => ['type' => 'integer'],
				    
				    'filename' => ['type' => 'keyword', 'index' => 'false'],
				    'vopros' => ['type' => 'integer', 'index' => 'false'],
				    'updated_at' => ['type' => 'integer', 'index' => 'false'],
				]
			]
		]);
	}
    
    public function actionIndex($cemetery_id = 0) {  	
        //$zags_list = Helper::regions();
        
        if ($cemetery_id) {
            $cemeteries = Cemetery::find()
				->andWhere(['id' => $cemetery_id])
				->andWhere(['deleted' => 0])
				->all();
			\app\models\HelperCache::deleteCemetery($cemetery_id);
		}
		else {
			$cemeteries = Cemetery::find()->andWhere(['deleted' => 0])->orderBy('id')->all();
			$this->createIndex();
		}

        foreach ($cemeteries as $cemetery) {
			echo $cemetery->name . PHP_EOL;
	
			$books = Book::find()
                   ->andWhere(['cemetery_id' => $cemetery->id])
                   ->all();

            \app\models\HelperCache::updateCache($books);
       	}
    }
}
