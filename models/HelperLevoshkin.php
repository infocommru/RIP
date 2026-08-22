<?php

namespace app\models;

class HelperLevoshkin {
    /**
     * @return int
     * @param Book $book
     */
    public static function update_unknown(Book $book): int {
        $updated = Record::updateAll([
            'is_unknown' => 1,
            ], [
                'and',
                    ['book_id' => $book->id],
                    ['or',
                        ['like', 'fio', 'неиз'],
                        ['like', 'fio', 'н/'],
                    ],
            ]);
            
        return $updated;
    }

    /**
     * @return array{day: int|null, month: int|null, year: int|null, date: string|null}
     * @param string $sDate
     */
	public static function getDate(string $sDate): array{
        $result = [
            'day' => null,
            'month' => null,
            'year' => null,
            'date' => null,
        ];

        if (preg_match("#(\d\d\d\d)#", $sDate, $m)) {
            $result['year'] = intval($m[1]);
        }

        if (!$result['year'])
            return $result;

        if (preg_match("#(\d\d?)\D(\d\d?)\D(\d\d\d\d)#", $sDate, $m)) {
            $result['day'] = intval(ltrim($m[1], '0'));
            $result['month'] = intval(ltrim($m[2], '0'));
        }

        if (preg_match("#(\d\d\d\d)\D(\d\d?)\D(\d\d?)#", $sDate, $m)) {
            $result['day'] = intval(ltrim($m[3], '0'));
            $result['month'] = intval(ltrim($m[2], '0'));
        }

        $result['date'] = $sDate;

        return $result;
    }

    /**
     * @return void
     * @param Record $record
     */
    public static function updateSearchRecord(Record $record): void {              
		$sfb = \app\models\CacheRecords::find()->query(['term' => ['record_id' => $record->id]])->one();

        if (!$sfb) {
            $sfb = new \app\models\CacheRecords();
            $sfb->_id = $record->id;
            $sfb->page_punkt = 0;
        }
        /** @var \app\models\CacheRecords $sfb */

        $result = \app\models\HelperCache::updateSearchRecord($record->toArray());

        $sfb->setAttributes($result);
        $sfb->save();
    }
}