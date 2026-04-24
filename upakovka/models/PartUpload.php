<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "part_upload".
 *
 * @property int $id
 * @property int $part_id
 * @property int $add_at
 * @property string $filename
 * @property int $status
 */
class PartUpload extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'part_upload';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['part_id', 'add_at', 'filename'], 'required'],
            [['part_id', 'add_at', 'status'], 'integer'],
            [['filename'], 'string', 'max' => 256],
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
            'add_at' => 'Add At',
            'filename' => 'Filename',
            'status' => 'Status',
        ];
    }
}
