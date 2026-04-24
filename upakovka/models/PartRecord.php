<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "part_record".
 *
 * @property int $id
 * @property int $part_id
 * @property int $record_id
 * @property int $status
 *
 * @property Part $part
 * @property Record $record
 */
class PartRecord extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'part_record';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'record_id'], 'required'],
            [['part_id', 'record_id', 'status'], 'integer'],
            [['part_id'], 'exist', 'skipOnError' => true, 'targetClass' => Part::class, 'targetAttribute' => ['part_id' => 'id']],
            [['record_id'], 'exist', 'skipOnError' => true, 'targetClass' => Record::class, 'targetAttribute' => ['record_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'part_id' => 'Part ID',
            'record_id' => 'Record ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[Part]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPart()
    {
        return $this->hasOne(Part::class, ['id' => 'part_id']);
    }

    /**
     * Gets query for [[Record]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRecord()
    {
        return $this->hasOne(Record::class, ['id' => 'record_id']);
    }
}
