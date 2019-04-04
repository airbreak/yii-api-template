<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 16:45
 */

namespace api\controllers;
use common\models\Article;
use yii\data\ActiveDataProvider;
use yii\rest\ActiveController;
use api\models\ApiLoginForm;


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
}