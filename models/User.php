<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property int|null $cemetery_id
 * @property int|null $book_id
 * @property string $username
 * @property string $password
 * @property string $firstname
 * @property string $lastname
 * @property string $middlename
 * @property string $position
 * @property int $role
 * @property Cemetery $cemetery
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface {

    /**
     * {@inheritdoc}
     */
    public static function tableName() {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            [['username', 'password', 'firstname', 'lastname'], 'required'],
            [['role', 'cemetery_id', 'book_id'], 'integer'],
            [['username', 'password'], 'string', 'max' => 64],
            [['firstname', 'lastname'], 'string', 'max' => 32],
            [['cemetery_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cemetery::class, 'targetAttribute' => ['cemetery_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => 'Логин',
            'password' => 'Пароль',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'middlename' => 'Отчество',
            'position' => 'Должность',
            'role' => 'Роль',
            'cemetery_id' => 'Кладбище',
            'book_id' => 'Книга',
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

    /**
     * Gets query for [[Book]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBook() {
        return $this->hasOne(Book::class, ['id' => 'book_id']);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id) {
        $user = self::find()->andWhere(['id' => $id])->one();
        return $user;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username) {
        $user = self::find()->andWhere(['username' => $username])->one();
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null) {
        $user = self::find()->andWhere(['username' => $token])->one();
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getId() {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey() {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey) {
        return $this->username === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password) {
        return $this->password === $password;
    }

    public static function roleList() {
        $roles = [
            1 => "Админ",
            2 => "Оператор",
            3 => "Проверяющий",
            4 => "Редактор",
        ];

        return $roles;
    }
}
