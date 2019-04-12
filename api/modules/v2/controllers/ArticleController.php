<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/2
 * Time: 16:45
 */

namespace api\modules\v2\controllers;
use common\models\Adminuser;
use common\models\Article;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\RateLimiter;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\web\Response;


class ArticleController extends ActiveController
{
    public $modelClass = 'common\models\Article';
    public function behaviors()
    {
//        return ArrayHelper::merge(parent::behaviors(), [
//            'authenticatior' => [
//                'class' => QueryParamAuth::className()
//            ]
//        ]);
        $behaviors = parent::behaviors();
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'enableRateLimitHeaders'=>true
        ];
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'authMethods' => [
                QueryParamAuth::className()
            ]
        ];
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        return $behaviors;
    }
//    public function behaviors()
//    {
//        return ArrayHelper::merge(parent::behaviors(), [
//            'authenticatior' => [
//                'class' => HttpBasicAuth::className(),
//                'auth' => function ($username, $password) {
//                    $user = Adminuser::find() ->where(['username' => $username])->one();
//                    if($user->validatePassword($password)) {
//                        return $user;
//                    }
//                    return null;
//                }
//            ]
//        ]);
//    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
//        return parent::actions(); // TODO: Change the autogenerated stub
    }
    public function actionIndex()
    {
        $modelClass = $this->modelClass;
        return new ActiveDataProvider(
            [
                'query' => $modelClass::find() -> asArray(),
                'pagination' => ['pageSize' => 5]
            ]
        );
    }
    public function actionSearch()
    {
        return Article::find()->where(['like','title',$_POST['keyword']])->all();
    }
}