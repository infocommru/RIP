<?php
namespace app\models;

use yii\elasticsearch\ActiveRecord;

class CacheRecords extends ActiveRecord
{
	public static $c_id;
	
    // Определяем атрибуты, которые будут храниться в OpenSearch
    public function attributes()
    {
        return [ "record_id", "cemetery_id", "regnum", "fam", "nam", "ot", "fio_display", "age", "age_int",
			"dead_year", "dead_month", "dead_day", "dead_date",
			"rip_year", "rip_month", "rip_day", "rip_date",
		 	"zags", "rip_style", "unknown", "unknown_number", "docnum", "areanum", "rownum", "ripnum",
			"relative", "svazka_num", "book_num", "page_num", "page_punkt", "comment", "comment_book",
			"book_id", "book_rip_style", "filename", "vopros", "updated_at"];
    }

    // Имя индекса в OpenSearch (аналог таблицы в БД)
    public static function index()
    {
    	if (self::$c_id !== null) {
            // Преобразуем в нижний регистр, так как ES не принимает заглавные буквы в именах индексов
            return 'cache_records_' . strtolower(self::$c_id);
        }
        
        return 'cache_records';
    }

    // Тип документа (для OpenSearch/Elasticsearch 7+ обычно используется '_doc')
    public static function type()
    {
        return '_doc';
    }

    // Настройка правил валидации (необязательно, но полезно)
    public function rules()
    {
        return [
            [['record_id', 'cemetery_id', 'age_int', 'dead_year', 'dead_month', 'dead_day', 
            	'rip_year', 'rip_month', 'rip_day', 'rip_style', 
            	'unknown', 'page_punkt', 'book_id', 'book_rip_style', 'vopros', 'updated_at'], 'integer'],
            [['regnum', 'unknown_number', 'svazka_num', 'book_num', 
            		'dead_date', 'rip_date'], 'string', 'max' => 32],
            [['fam', 'nam', 'ot', 'page_num'], 'string', 'max' => 64],
            [['docnum', 'fio_display', 'age'], 'string', 'max' => 128],
            [['zags', 'areanum', 'rownum', 'ripnum', 'relative', 'filename'], 'string', 'max' => 256],
            [['comment', 'comment_book'], 'string'],
        ];
    }
}
