<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "part".
 *
 * @property int $id
 * @property int|null $cemetery_id
 * @property int $number
 * @property int|null $user_id
 * @property int|null $records
 * @property int $add_at
 * @property int $status
 * @property int $status_result
 * @property int $update_flag
 *
 * @property Book[] $books
 * @property Cemetery $cemetery
 * @property PartRecord[] $partRecords
 * @property User $user
 */
class Part extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'part';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cemetery_id', 'number', 'user_id', 'records', 'add_at', 'status', 'status_result', 'update_flag'], 'integer'],
            [['cemetery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cemetery::class, 'targetAttribute' => ['cemetery_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cemetery_id' => 'Cemetery ID',
            'number' => 'Номер',
            'user_id' => 'User ID',
            'records' => 'Кол-во записей',
            'add_at' => 'Add At',
            'status' => 'Статус',
            'status_result' => 'Status Result',
            'update_flag' => 'Update Flag',
        ];
    }

    /**
     * Gets query for [[Books]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBooks()
    {
        return $this->hasMany(Book::class, ['part_id' => 'id']);
    }

    /**
     * Gets query for [[Cemetery]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCemetery()
    {
        return $this->hasOne(Cemetery::class, ['id' => 'cemetery_id']);
    }

    /**
     * Gets query for [[PartRecords]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartRecords()
    {
        return $this->hasMany(PartRecord::class, ['part_id' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
