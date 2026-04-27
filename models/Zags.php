<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zags".
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $deleted
 *
 * @property ZagsRaw[] $zagsRaws
 */
class Zags extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'zags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['name'], 'required'],
            [['description'], 'string'],
            [['deleted'], 'integer'],
            [['name'], 'string', 'max' => 256],
        ];
    }

    public static function delStatuses() {
        return [0 => 'Да', 1 => 'Нет'];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'description' => 'Описание',
            'deleted' => 'Активно',
        ];
    }

    /**
     * Gets query for [[ZagsRaws]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZagsRaws() {
        return $this->hasMany(ZagsRaw::class, ['zags_id' => 'id']);
    }
}
