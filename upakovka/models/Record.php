<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "record".
 *
 * @property int $id
 * @property int $book_id
 * @property int $user_id
 * @property int $numReg
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
 * @property string|null $filename2
 * @property string|null $comment
 * @property int|null $rip_style
 * @property int|null $updated_at
 * @property int $vopros
 * @property int $is_unknown
 *
 * @property Book $book
 * @property User $user
 */
class Record extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['book_id'], 'required'],
            [['book_id', 'numReg', 'rip_style', 'updated_at',
            'user_id', 'vopros', 'is_unknown'], 'integer'],
            [['comment'], 'string'],
            [['numLiteral', 'age', 'death_date', 'rip_date', 'docnum'], 'string', 'max' => 32],
            [['fio', 'zags', 'area_num', 'row_num', 'rip_num'], 'string', 'max' => 128],
            [['riper'], 'string', 'max' => 64],
            [['relative_fio', 'filename', 'filename2'], 'string', 'max' => 256],
            [['book_id'], 'exist', 'skipOnError' => true, 'targetClass' => Book::class, 'targetAttribute' => ['book_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'book_id' => 'Книга',
            'user_id' => 'Пользователь',
            'numReg' => 'Номер записи',
            'numLiteral' => 'Номер букв.',
            'fio' => 'ФИО',
            'age' => 'Возраст',
            'death_date' => 'Дата смерти',
            'rip_date' => 'Дата захоронения',
            'docnum' => 'Номер документа ЗАГС',
            'zags' => 'ЗАГС',
            'riper' => 'Землекоп',
            'area_num' => 'Номер участка',
            'row_num' => 'Номер ряда',
            'rip_num' => 'Номер могилы',
            'relative_fio' => 'Родственники',
            'filename' => 'Файл', 'filename2' => 'Файл2',
            'comment' => 'Комментарий',
            'rip_style' => 'Захоронение',
            'updated_at' => 'Обновлено',
            'vopros' => 'Есть вопросы',
            'is_unknown' => 'Неизвестный',
        ];
    }

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook() {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public static function ripStyleTypes() {
        $types = [
            1 => "Гроб",
            2 => "Урна",
        ];

        return $types;
    }

    public function search($params) {
        
    }
}
