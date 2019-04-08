<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "adminuser".
 *
 * @property int $id ID
 * @property string $username 用户名
 * @property string $realname 姓名
 * @property string $email 电子邮箱
 * @property int $status 状态
 * @property string $password_hash 密码
 * @property string $auth_key 授权key
 * @property string $password_reset_token 密码重置token
 * @property string $access_token 访问token
 * @property int $expire_at 过期时间
 * @property int $logged_at 登入时间
 * @property int $created_at 创建时间
 * @property int $updated_at 最后修改时间
 */
class Adminuser extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adminuser';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'email', 'password_hash', 'auth_key'], 'required'],
            [['id', 'status', 'expire_at', 'logged_at', 'created_at', 'updated_at'], 'integer'],
            [['username'], 'string', 'max' => 32],
            [['realname', 'email', 'password_hash', 'auth_key', 'password_reset_token', 'access_token'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}findIdentityByAccessToken
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => '用户名',
            'realname' => '姓名',
            'email' => '电子邮箱',
            'status' => '状态',
            'password_hash' => '密码',
            'auth_key' => '授权key',
            'password_reset_token' => '密码重置token',
            'access_token' => '访问token',
            'expire_at' => '过期时间',
            'logged_at' => '登入时间',
            'created_at' => '创建时间',
            'updated_at' => '最后修改时间',
        ];
    }
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
        return $this->access_token;
    }
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static ::find()
            ->where(['access_token'=> $token,'status'=> self::STATUS_ACTIVE])
            ->andWhere(['>', 'expire_at', time()])
            ->one();
    }
    /**
     * @inheritdoc
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
}
