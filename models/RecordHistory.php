<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "record_history".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int $record_id
 * @property string $info
 * @property int $updated_at
 *
 * @property Record $record
 * @property User $user
 */
class RecordHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'record_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'record_id', 'updated_at'], 'integer'],
            [['record_id', 'info'], 'required'],
            [['info'], 'string'],
            [['record_id'], 'exist', 'skipOnError' => true, 'targetClass' => Record::class, 'targetAttribute' => ['record_id' => 'id']],
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
            'user_id' => 'User ID',
            'record_id' => 'Record ID',
            'info' => 'Info',
            'updated_at' => 'Updated At',
        ];
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
