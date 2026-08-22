<?php
namespace app\models;

use yii\elasticsearch\ActiveRecord;

/**
 * Модель для индекса cache_records в OpenSearch.
 *
 * @property int|null $_id Внутренний ID документа в Elasticsearch
 * @property int $record_id
 * @property int $cemetery_id
 * @property string|null $regnum
 * @property string|null $fam
 * @property string|null $nam
 * @property string|null $ot
 * @property string|null $fio_display
 * @property string|null $age
 * @property int|null $age_int
 * @property int|null $dead_year
 * @property int|null $dead_month
 * @property int|null $dead_day
 * @property string|null $dead_date
 * @property int|null $rip_year
 * @property int|null $rip_month
 * @property int|null $rip_day
 * @property string|null $rip_date
 * @property string|null $zags
 * @property int|null $rip_style
 * @property int|null $unknown
 * @property string|null $unknown_number
 * @property string|null $docnum
 * @property string|null $areanum
 * @property string|null $rownum
 * @property string|null $ripnum
 * @property string|null $relative
 * @property string|null $svazka_num
 * @property string|null $book_num
 * @property string|null $page_num
 * @property int $page_punkt
 * @property string|null $comment
 * @property string|null $comment_book
 * @property int $book_id
 * @property int $book_rip_style
 * @property string|null $filename
 * @property int $vopros
 * @property int|null $updated_at
 */

class CacheRecords extends ActiveRecord
{
	
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
