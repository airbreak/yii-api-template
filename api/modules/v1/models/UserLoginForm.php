<?php
namespace api\modules\v1\models;

use Yii;
use yii\base\Model;
use common\models\User;

/**
 * 普通的微信用户登录
 */
class UserLoginForm extends Model
{
    public $code;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['code'], 'required'],
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {

            //用code兑换openid

            $access_token = $this->_user->generateAccessToken();
            $duration = 24* 3600 * 7;
            $this->_user->expire_at = time() + $duration;
            $this->_user->save();
            yii::$app->user->login($this->_user, $duration);
            return $access_token;
        } else {
            return false;
        }
    }

    public function getOpenid()
    {

    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = Adminuser::findByUsername($this->username);
        }

        return $this->_user;
    }
}
