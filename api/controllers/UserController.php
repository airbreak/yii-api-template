<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 9:10
 */

namespace api\controllers;
use phpDocumentor\Reflection\DocBlock\Tags\Param;
use yii\rest\ActiveController;
use api\models\LoginForm;

class UserController extends ActiveController
{
    public $modelClass = 'common\models\User';
    public function actions()
    {
        $action = parent::actions();
        unset($action['index']);
        unset($action['create']);
        unset($action['update']);
        unset($action['delete']);
    }
    public function actionIndex()
    {

    }
    public function actionSendEmail()
    {
        return 'a';
    }
    public  function actionLogin()
    {
        $model = new LoginForm();
        if($model->load(\Yii::$app->getRequest()->getBodyParams(), '')) {
            if($model ->login()) {
                return [
                    'access_token' => $model->login(),
                ];
            } else {
                return [
                    'access_token' => 'no'
                ];
            }
        } else {
            return $model -> getFirstErrors();
        }
    }
}