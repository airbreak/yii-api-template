<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 16:45
 */

namespace api\controllers;
use yii;
use yii\rest\ActiveController;
use api\models\ApiLoginForm;
use api\models\ApiSignupForm;


class AdminuserController extends ActiveController
{
    public $modelClass = 'common\models\Adminuser';
    public function actionLogin()
    {
        $model = new ApiLoginForm();
        $model->username =  $_POST['username'];
        $model->password = $_POST['password'];
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