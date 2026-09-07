<?php

namespace app\models;
use yii\helpers\FileHelper;

class HelperCache {
    /**
     * @return void
     * @param int $cemetery
     */
    public static function deleteCemetery(int $cemetery) {
        $response = \Yii::$app->elasticsearch->post(
            [ \app\models\CacheRecords::index(), '_delete_by_query' ],
            ['wait_for_completion' => 'false',],
            json_encode([
                'query' => [
                    'term' => ['cemetery_id' => $cemetery]
                ]
            ])
        );
    }

    /**
     * @return void
     * @param int $book
     */
    public static function deleteBook(int $book) {
        $response = \Yii::$app->elasticsearch->post(
            [ \app\models\CacheRecords::index(), '_delete_by_query' ],
            ['wait_for_completion' => 'false',],
            json_encode([
                'query' => [
                    'term' => ['book_id' => $book]
                ]
            ])
        );
    }

     /**
     * @return void
     * @param array<Book> $books
     * @param (\Closure(): void)|null $updateStatus
     */
	public static function updateCache(array $books, ?\Closure $updateStatus = null) {
        $batchRows = CacheRecords::getDb()->createBulkCommand();
        $counter = 0;

        foreach ($books as $book) {
            $records = \app\models\Record::find()
                ->andWhere(['book_id' => $book->id])
                ->andWhere(['deleted' => 0])
                ->orderBy('id')
                ->asArray()
                ->all();

            $lastPage = 'asdasd';
            $lastPagePunkt = 1;

            if($updateStatus){
                $updateStatus();
            }

            foreach ($records as $record) {
                $result = self::updateSearchRecord($record, $book);

                if ($result['page_num'] != $lastPage) {
                    $lastPage = $result['page_num'];
                    $lastPagePunkt = 1;
                }

                $page_punkt = $lastPagePunkt++;

                $batchRows->addAction([
                    'index' => [
                        '_index' => \app\models\CacheRecords::index(),
                        '_id'    => $record['id'],
                    ]
                ],
                [
                    'record_id' => $result['record_id'],
                    'cemetery_id' => $result['cemetery_id'],
                    'regnum' => $result['regnum'],
                    'fam' => $result['fam'],
                    'nam' => $result['nam'],
                    'fio_display' => $result['fio_display'],
                    'ot' => $result['ot'],
                    'age' => $result['age'],
                    'age_int' => $result['age_int'],
                    'dead_year' => $result['dead_year'],
                    'dead_month' => $result['dead_month'],
                    'dead_day' => $result['dead_day'],
                    'dead_date' => $result['dead_date'],
                    'rip_year' => $result['rip_year'],
                    'rip_month' => $result['rip_month'],
                    'rip_day' => $result['rip_day'],
                    'rip_date' => $result['rip_date'],
                    'zags' => $result['zags'],
                    'rip_style' => $result['rip_style'],
                    'unknown' => $result['unknown'],
                    'unknown_number' => $result['unknown_number'],
                    'docnum' => $result['docnum'],
                    'areanum' => $result['areanum'],
                    'rownum' => $result['rownum'],
                    'ripnum' => $result['ripnum'],
                    'relative' => $result['relative'],
                    'svazka_num' => $result['svazka_num'],
                    'book_num' => $result['book_num'],
                    'page_num' => $result['page_num'],
                    'page_punkt' => $page_punkt,
                    'comment' => $result['comment'],
                    'comment_book' => $result['comment_book'],
                    'book_id' => $result['book_id'],
                    'book_rip_style' => $result['book_rip_style'],
                    'filename' => $result['filename'],
                    'vopros' => $result['vopros'],
                    'updated_at' => $result['updated_at'],
                ]);

                if ($counter >= 5000) {
                    $response = $batchRows->execute();
                    $batchRows = CacheRecords::getDb()->createBulkCommand();
                    $counter = 0;
                }

                $counter++;
            }
        }

        if ($counter > 0) {
            $response = $batchRows->execute();
            $batchRows = CacheRecords::getDb()->createBulkCommand();
            $counter = 0;
        }
    }

    public static function splitFIO(string $FIO): array {
        $value = [
            'fam' => '',
            'nam' => '',
            'ot' => ''
        ];

        $FIO = trim(preg_replace('/\s+/', ' ', trim($FIO)));
        
        if ($FIO) {
            $ff = explode(" ", $FIO);
            $value["fam"] = $ff[0];

            if (sizeof($ff) > 1)
                $value["nam"] = $ff[1];
            if (sizeof($ff) > 2)
                $value["ot"] = $ff[2];
        }

        return $value;
    }

    /**
     *
     * @param array<string, mixed> $record
     * @param Book|null $book
     * @return array<string, mixed>
     */
    public static function updateSearchRecord(array $record, ?Book $book = null): array{
        if ($book === null){
            $book = Book::find()
                ->andWhere(['id' => $record['book_id']])
                ->one();
        }

        $value = [];

        $value["fio_display"] = preg_replace('/\s+/', ' ', trim((string)$record['fio']));
        $value = array_merge($value, self::splitFIO($value["fio_display"]));

        $deadYearInf = \app\models\HelperLevoshkin::getDate((string)$record['death_date']);
        $ripYearInf = \app\models\HelperLevoshkin::getDate((string)$record['rip_date']);

        if ($record['numReg'] !== null)
            $value["regnum"] = (string)$record['numReg'];
        else
            $value["regnum"] = (string)$record['numLiteral'];

        $value["unknown_number"] = null;

        if (preg_match("#№\s+([\d\\/]+)#", (string)$record['fio'], $m)) {
            $value["unknown_number"] = $m[1];
        } else {
            if ($record['is_unknown']) {
                if (preg_match("#.*?(\d[\d\\/]+).*?#", (string)$record['fio'], $m)) {
                    $value["unknown_number"] = $m[1];
                } else {
                    if (preg_match("#.*?(\d+).*?#", (string)$record['fio'], $m)) {
                        $value["unknown_number"] = $m[1];
                    }
                }
            }
        }

        $basename = FileHelper::normalizePath((string)$record['filename']);
        $basename = pathinfo($basename, PATHINFO_FILENAME);
        $value["page_num"] = ltrim($basename, "0") ?: '0';

        $value['record_id'] = $record['id'];
        $value['cemetery_id'] = $book->cemetery_id;
        $value['age'] = (string)$record['age'];
        $value['age_int'] = ((int)$record['age'] > 200) ? null : (int)$record['age'];
        $value['dead_year'] = $deadYearInf['year'];
        $value['dead_month'] = $deadYearInf['month'];
        $value['dead_day'] = $deadYearInf['day'];
        $value['dead_date'] = $deadYearInf['date'];
        $value['rip_year'] = $ripYearInf['year'];
        $value['rip_month'] = $ripYearInf['month'];
        $value['rip_day'] = $ripYearInf['day'];
        $value['rip_date'] = $ripYearInf['date'];
        $value['zags'] = (string)$record['zags'];
        $value['rip_style'] = $record['rip_style'];
        $value['unknown'] = $record['is_unknown'];
        $value['docnum'] = (string)$record['docnum'];
        $value['areanum'] = (string)$record['area_num'];
        $value['rownum'] = (string)$record['row_num'];
        $value['ripnum'] = (string)$record['rip_num'];
        $value['relative'] = (string)$record['relative_fio'];
        $value['svazka_num'] = $book->svazka;
        $value['book_num'] = $book->number;
        $value['comment'] = (string)$record['comment'];
        $value['comment_book'] = (string)$book->comment;
        $value['book_id'] = $book->id;
        $value['book_rip_style'] = $book->rip_style;
        $value['filename'] = (string)$record['filename'];
        $value['vopros'] = (string)$record['vopros'];
        $value['updated_at'] = $record['updated_at'];

        return $value;
    }
}