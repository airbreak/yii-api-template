<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 16:45
 */

namespace api\modules\v1\controllers;
use yii;
use yii\rest\ActiveController;
use api\modules\v1\models\UserLoginForm;


class UserController extends ActiveController
{
    public $modelClass = 'common\models\Adminuser';
    public function actionLogin()
    {
        $model = new UserLoginForm();
        $model->code =  $_POST['code'];
        if($model->login()) {
            return ['access_token' => $model->login()];
        } else {
            $model->validate();
            return $model;
        }
    }
    public function actionSignup()
    {
        Yii::trace('signup---------start-------');
        $model = new ApiSignupForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
//        $model->username =  $_POST['username'];
//        $model->password = $_POST['password'];
        if($model->signup()) {
            return ['result' => '注册成功'];
        } else {
            $model->validate();
            return $model;
        }
    }
}