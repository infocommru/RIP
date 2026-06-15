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

	private function createIndex($table_name) {
		$db = CacheRecords::getDb();
		$command = $db->createCommand();

		if ($command->indexExists(\app\models\CacheRecords::index()))
			$command->deleteIndex(\app\models\CacheRecords::index());

		$command->createIndex($table_name, [
			'settings' => [
				'number_of_shards' => 1,
				'number_of_replicas' => 0,
				'analysis' => [
					'tokenizer' => [
						'autocomplete_tokenizer' => [
							'type' => 'edge_ngram',
							'min_gram' => 2,
							'max_gram' => 20,
							'token_chars' => ['letter', ' digit']
						]
					],
					'analyzer' => [
						'autocomplete' => [
							'tokenizer' => 'autocomplete_tokenizer',
							'filter' => 'lowercase'
						]
					]
				]
			],
			'mappings' => [
				'properties' => [
				    'record_id' => ['type' => 'integer'],
					'cemetery_id' => ['type' => 'integer'],
				    'regnum' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
				    'fam' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
				    'nam' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
				    'ot' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
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
				    
				    'zags' => ['type' => 'text', 'analyzer' => 'russian', 'fields' => ['autocomplete' => [
				    	'type' => 'text', 
				    	'analyzer' => 'autocomplete',
				    	'search_analyzer' => 'russian']]],
				    'rip_style' => ['type' => 'integer'],
				    
				    'unknown' => ['type' => 'integer'],
				    'unknown_number' => ['type' => 'wildcard', 'normalizer' => 'lowercase'],
				    
				    'docnum' => ['type' => 'wildcard', 'normalizer' => 'lowercase'],
				    'areanum' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
				    'rownum' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
				    'ripnum' => ['type' => 'keyword', 'normalizer' => 'lowercase', 
				    	'fields' => ['wildcard' => [
							'type' => 'wildcard',
							'normalizer' => 'lowercase'
					]]],
				    'relative' => ['type' => 'text', 'analyzer' => 'russian', 'fields' => ['autocomplete' => [
				    	'type' => 'text', 
				    	'analyzer' => 'autocomplete',
				    	'search_analyzer' => 'russian']]],
				    
				    'svazka_num' => ['type' => 'keyword', 'index' => 'false'],
				    'book_num' => ['type' => 'keyword', 'index' => 'false'],
				    'page_num' => ['type' => 'keyword', 'index' => 'false'],
				    'page_punkt' => ['type' => 'integer'],
				    
				    'comment' => ['type' => 'text', 'analyzer' => 'russian', 'fields' => ['autocomplete' => [
				    	'type' => 'text', 
				    	'analyzer' => 'autocomplete',
				    	'search_analyzer' => 'russian']]],
				    'comment_book' => ['type' => 'text', 'analyzer' => 'russian', 'fields' => ['autocomplete' => [
				    	'type' => 'text', 
				    	'analyzer' => 'autocomplete',
				    	'search_analyzer' => 'russian']]],
				    
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
			$this->createIndex(\app\models\CacheRecords::index());
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
