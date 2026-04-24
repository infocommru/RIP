<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "book".
 *
 * @property int $id
 * @property int $cemetery_id
 * @property int|null $part_id
 * @property string $name
 * @property string|null $number
 * @property string|null $svazka
 * @property string $year1
 * @property string $year2
 * @property string $records
 * @property string $per_page
 * @property int $status
 * @property string $comment
 * @property int $rip_style
 * @property int $deleted 
 * 
 * @property Cemetery $cemetery
 */
class Book extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['cemetery_id', 'name', 'records'], 'required'],
            [['cemetery_id', 'part_id', 'per_page', 'status'], 'integer'],
            [['number', 'svazka'], 'string', 'max' => 128],
            [['year1', 'year2', 'records'], 'string', 'max' => 32],
            [['cemetery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cemetery::class, 'targetAttribute' => ['cemetery_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'cemetery_id' => 'Кладбище',
            'part_id' => 'Партия',
            'name' => 'Название',
            'number' => 'Номер книги',
            'svazka' => 'Номер связки',
            'year1' => 'Год начала',
            'year2' => 'Год окончания',
            'records' => 'Записи',
            'per_page' => 'Записей на страницу',
            'status' => 'Статус',
            'comment' => 'Комментарий',
            'rip_style' => 'Захоронение',
            'deleted' => 'Удалено',
        ];
    }

    /**
     * Gets query for [[Cemetery]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCemetery() {
        return $this->hasOne(Cemetery::class, ['id' => 'cemetery_id']);
    }

    public static function getStatuses() {
        $statuses = [
            1 => 'свободен',
            2 => 'обрабатывается оператором',
            3 => 'закончена оператором',
            4 => 'проверена оператором',
            5 => 'проверяется админом',
            6 => 'проверена админом',
        ];

        return $statuses;
    }

    /**
     * Gets query for [[Part]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPart() {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    public static function ripStyleTypes() {
        $types = [
            0 => "-",
            1 => "Гроб",
            2 => "Урна",
                        3 => "Урна, стена",
            4 => "Урна, земля",
        ];

        return $types;
    }
}
