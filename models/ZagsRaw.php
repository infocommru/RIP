<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zags_raw".
 *
 * @property int $id
 * @property int|null $zags_id
 * @property int|null $zags_simular_id
 * @property string|null $percent
 * @property string $name
 * @property int $cnt
 *
 * @property Zags $zags
 */
class ZagsRaw extends \yii\db\ActiveRecord {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'zags_raw';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['zags_id', 'zags_simular_id', 'percent', 'cnt'], 'integer'],
            [['name'], 'required'],
            //[['percent'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 256],
            [['zags_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zags::class, 'targetAttribute' => ['zags_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'zags_id' => 'ЗАГС',
            'zags_simular_id' => 'Похожий ЗАГС',
            'percent' => 'Схожесть',
            'name' => 'Название',
            'cnt' => 'Количество',
        ];
    }

    /**
     * Gets query for [[Zags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZags() {
        return $this->hasOne(Zags::class, ['id' => 'zags_id']);
    }
}
