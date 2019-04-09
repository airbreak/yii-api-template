<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\base\NotSupportedException;
use yii\filters\RateLimitInterface;
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
 * @property int $allowance 创建时间
 * @property int $allowance_updated_at 最后修改时间
 */
class Adminuser extends ActiveRecord implements IdentityInterface, RateLimitInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function allStatus()
    {
        return [self::STATUS_ACTIVE=>'正常',self::STATUS_DELETED=>'禁用'];
    }

    public function getStatusStr()
    {
        return $this->status==self::STATUS_ACTIVE?'正常':'禁用';
    }
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
//    public function rules()
//    {
//        return [
//            [['id', 'email', 'password_hash', 'auth_key'], 'required'],
//            [['id', 'status', 'expire_at', 'logged_at', 'created_at', 'updated_at'], 'integer'],
//            [['username'], 'string', 'max' => 32],
//            [['realname', 'email', 'password_hash', 'auth_key', 'password_reset_token', 'access_token'], 'string', 'max' => 255],
//        ];
//    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['email', 'password_hash', 'auth_key'], 'required'],
            [['status', 'expire_at', 'logged_at', 'created_at', 'updated_at','allowance','allowance_updated_at'], 'integer'],
            [['username'], 'string', 'max' => 32],
            [['realname', 'email', 'password_hash', 'auth_key', 'password_reset_token', 'access_token'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['access_token'], 'unique'],
            [['username'], 'unique'],
            [['password_reset_token'], 'unique'],
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
            'allowance' => '允许访问次数',
            'allowance_updated_at' => '允许访问次数最后修改时间',
        ];
    }
    public function beforeSave($insert)
    {
        if(parent::beforeSave($insert)) {
            if($insert) {
                $this->created_at = time();
                $this->updated_at = time();
            } else {
                $this->updated_at = time();
            }
            return true;
        } else {
            return false;
        }
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
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
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
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
        ]);
    }
    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }
    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }
    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRateLimit($request, $action)
    {
        return [3, 10]; // 10s 中之内，只能调用3次
    }
    public  function  loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }
    public  function  saveAllowance($request, $action, $allowance, $timestamp)
    {
        $this->allowance= $allowance;
        $this->allowance_updated_at = $timestamp;
        $this->save();
    }
}
