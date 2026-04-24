<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "__search_form_2".
 *
 * @property int $id
 * @property int $record_id
 * @property string|null $regnum
 * @property string|null $fam
 * @property string|null $nam
 * @property string|null $ot
 * @property int|null $age
 * @property int|null $dead_year
 * @property int|null $rip_year
 * @property int $zags_num
 * @property int $rip_style
 * @property int $unknown
 * @property string|null $unknown_number
 * @property string|null $docnum
 * @property string|null $areanum
 * @property string|null $rownum
 * @property string|null $ripnum
 * @property string|null $relative
 * @property string|null $svazka_num
 * @property string|null $book_num
 * @property string|null $page_num
 * @property int|null $page_punkt
 * 
 * @property Record $record

 */
class SearchFormBasic extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return $GLOBALS['search_form_table'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['record_id', 'age', 'dead_year', 'rip_year', 'zags_num', 'rip_style',
            'page_punkt', 'unknown',
                ], 'integer'],
            [['fam', 'nam', 'ot', 'docnum', 'page_num'], 'string', 'max' => 256],
            [['areanum', 'rownum', 'ripnum'], 'string', 'max' => 256],
            [['relative'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => 'Запись',
            'regnum' => 'Номер записи',
            'fam' => 'Fam',
            'nam' => 'Nam',
            'ot' => 'Ot',
            'age' => 'Age',
            'dead_year' => 'Dead Year',
            'rip_year' => 'Rip Year',
            'zags_num' => 'Zags Num',
            'unknown' => 'Zags Num',
            'unknown_number' => 'Zags Num',
            'rip_style' => 'Rip Style',
            'docnum' => 'Docnum',
            'areanum' => 'Areanum',
            'rownum' => 'Rownum',
            'ripnum' => 'Ripnum',
            'relative' => 'Relative',
            'svazka_num' => '',
            'book_num' => '',
            'page_num' => '',
            'page_punkt' => '',
        ];
    }

    /**
     * Gets query for [[Record]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecord() {
        return $this->hasOne(Record::class, ['id' => 'record_id']);
    }
}
