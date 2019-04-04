<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/4/4
 * Time: 15:42
 */
namespace api\controllers;
use yii\rest\Controller;
use yii\db\Query;
use common\models\Article;

class Top10Controller extends Controller
{
    public function actionIndex()
    {
        $top10 = (new Query())
            ->from('article')
            ->select(['created_by','Count(id) as creatercount'])
            ->groupBy(['created_by'])
            ->orderBy('creatercount DESC')
            ->limit(10)
            ->all();
        return $top10;
    }
}