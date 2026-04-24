<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "book_upload".
 *
 * @property int $id
 * @property int $cemetery_id
 * @property int $add_at
 * @property string $filename
 * @property int $status
 * @property int $part_flag
 *
 * @property Cemetery $cemetery
 */
class BookUpload extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'book_upload';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cemetery_id', 'add_at', 'filename'], 'required'],
            [['cemetery_id', 'add_at', 'status', 'part_flag'], 'integer'],
            [['filename'], 'string', 'max' => 128],
            [['cemetery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cemetery::class, 'targetAttribute' => ['cemetery_id' => 'id']],
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
            'add_at' => 'Add At',
            'filename' => 'Filename',
            'status' => 'Status',
            'part_flag' => 'Part Flag',
        ];
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
}
