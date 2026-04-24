<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "record2".
 *
 * @property int $id
 * @property int|null $book_id
 * @property int|null $user_id
 * @property int|null $numReg
 * @property string|null $numLiteral
 * @property string|null $fio
 * @property string|null $age
 * @property string|null $death_date
 * @property string|null $rip_date
 * @property string|null $docnum
 * @property string|null $zags
 * @property string|null $riper
 * @property string|null $area_num
 * @property string|null $row_num
 * @property string|null $rip_num
 * @property string|null $relative_fio
 * @property string|null $filename
 * @property string|null $comment
 * @property int|null $rip_style
 * @property int|null $updated_at
 */
class Record2 extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'record2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['book_id', 'user_id', 'numReg', 'rip_style', 'updated_at'], 'integer'],
            [['comment'], 'string'],
            [['numLiteral', 'age', 'death_date', 'rip_date', 'docnum'], 'string', 'max' => 32],
            [['fio', 'zags', 'area_num', 'row_num', 'rip_num'], 'string', 'max' => 128],
            [['riper'], 'string', 'max' => 64],
            [['relative_fio', 'filename'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'book_id' => 'Book ID',
            'user_id' => 'User ID',
            'numReg' => 'Num Reg',
            'numLiteral' => 'Num Literal',
            'fio' => 'Fio',
            'age' => 'Age',
            'death_date' => 'Death Date',
            'rip_date' => 'Rip Date',
            'docnum' => 'Docnum',
            'zags' => 'Zags',
            'riper' => 'Riper',
            'area_num' => 'Area Num',
            'row_num' => 'Row Num',
            'rip_num' => 'Rip Num',
            'relative_fio' => 'Relative Fio',
            'filename' => 'Filename',
            'comment' => 'Comment',
            'rip_style' => 'Rip Style',
            'updated_at' => 'Updated At',
        ];
    }
}
